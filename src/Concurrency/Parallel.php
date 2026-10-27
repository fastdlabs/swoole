<?php

declare(strict_types=1);

namespace FastD\Swoole\Concurrency;

use InvalidArgumentException;
use RuntimeException;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use Throwable;

/**
 * 运行时自适应的并发任务执行器。
 *
 * - 在 Swoole 协程上下文中：并发执行，按总超时控制
 * - 在 CGI / 非协程上下文：按任务顺序串行执行
 *
 * 同一个调用方代码无需判断运行环境。
 */
final class Parallel
{
    /**
     * 是否处于 Swoole 协程上下文。
     */
    public static function isConcurrent(): bool
    {
        return extension_loaded('swoole')
            && class_exists(Coroutine::class)
            && Coroutine::getCid() > 0;
    }

    /**
     * 执行一组任务并保持键名。
     *
     * @template T
     * @param array<array-key, callable(): T> $tasks
     * @return array<array-key, T>
     *
     * @throws ParallelTimeoutException
     */
    public static function all(array $tasks, ?float $timeout = null): array
    {
        if ($tasks === []) {
            return [];
        }

        if ($timeout !== null && $timeout < 0) {
            throw new InvalidArgumentException('Timeout must be null or >= 0.');
        }

        $executor = new self();

        return self::isConcurrent()
            ? $executor->runConcurrent($tasks, $timeout)
            : $executor->runSerial($tasks, $timeout);
    }

    /**
     * @param array<array-key, callable(): mixed> $tasks
     * @return array<array-key, mixed>
     */
    private function runSerial(array $tasks, ?float $timeout): array
    {
        $startedAt = microtime(true);
        $results = [];

        foreach ($tasks as $key => $task) {
            if ($timeout !== null && microtime(true) - $startedAt > $timeout) {
                throw new ParallelTimeoutException(sprintf('Parallel tasks exceeded %.3fs.', $timeout));
            }

            $results[$key] = $task();
        }

        return $results;
    }

    /**
     * @param array<array-key, callable(): mixed> $tasks
     * @return array<array-key, mixed>
     */
    private function runConcurrent(array $tasks, ?float $timeout): array
    {
        $channel = new Channel(count($tasks));
        $deadline = $timeout === null ? null : microtime(true) + $timeout;
        $cids = [];

        foreach ($tasks as $key => $task) {
            $cid = Coroutine::create(static function () use ($channel, $key, $task): void {
                try {
                    $channel->push([
                        'key' => $key,
                        'value' => $task(),
                        'error' => null,
                    ]);
                } catch (Throwable $throwable) {
                    $channel->push([
                        'key' => $key,
                        'value' => null,
                        'error' => $throwable,
                    ]);
                }
            });

            if ($cid === false) {
                throw new RuntimeException(sprintf('Unable to create coroutine for task "%s".', (string) $key));
            }

            $cids[$key] = $cid;
        }

        $results = [];

        for ($i = 0, $count = count($tasks); $i < $count; $i++) {
            $remaining = $deadline === null ? -1 : max(0.0, $deadline - microtime(true));
            $message = $channel->pop($remaining);

            if ($message === false) {
                $this->cancel($cids);
                throw new ParallelTimeoutException(
                    $timeout === null ? 'Parallel task channel closed.' : sprintf('Parallel tasks exceeded %.3fs.', $timeout)
                );
            }

            if (($message['error'] ?? null) instanceof Throwable) {
                $this->cancel($cids);
                throw $message['error'];
            }

            $results[$message['key']] = $message['value'];
            unset($cids[$message['key']]);
        }

        $ordered = [];
        foreach (array_keys($tasks) as $key) {
            $ordered[$key] = $results[$key];
        }

        return $ordered;
    }

    /**
     * @param array<array-key, int|false> $cids
     */
    private function cancel(array $cids): void
    {
        foreach ($cids as $cid) {
            if (is_int($cid) && $cid > 0) {
                Coroutine::cancel($cid);
            }
        }
    }
}
