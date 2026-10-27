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
     * 聚合执行，返回与输入键一致的结果数组。
     *
     * @template T
     * @param array<array-key, callable(): T> $tasks
     * @return array<array-key, T>
     *
     * @throws ParallelTimeoutException
     */
    public static function all(array $tasks, ?float $timeout = null): array
    {
        $results = [];

        self::each($tasks, $timeout, static function (int|string $key, mixed $value) use (&$results): void {
            $results[$key] = $value;
        });

        $ordered = [];
        foreach (array_keys($tasks) as $key) {
            $ordered[$key] = $results[$key];
        }

        return $ordered;
    }

    /**
     * 执行一组任务。
     *
     * - 不传回调：收集结果并返回
     * - 传回调：完成一个回调一个，不保存结果，返回空数组
     *
     * @param array<array-key, callable(): mixed> $tasks
     * @param callable(int|string, mixed): void|null $onResult
     * @return array<array-key, mixed>
     *
     * @throws ParallelTimeoutException
     */
    public static function each(
        array $tasks,
        ?float $timeout = null,
        ?callable $onResult = null
    ): array {
        $results = [];
        $collect = $onResult === null;

        $consumer = $collect
            ? static function (int|string $key, mixed $value) use (&$results): void {
                $results[$key] = $value;
            }
            : $onResult;

        (new self())->execute($tasks, $consumer, $timeout);

        return $collect ? $results : [];
    }

    /**
     * @param array<array-key, callable(): mixed> $tasks
     * @param callable(int|string, mixed): void $onResult
     */
    private function execute(array $tasks, callable $onResult, ?float $timeout): void
    {
        if ($tasks === []) {
            return;
        }

        if ($timeout !== null && $timeout < 0) {
            throw new InvalidArgumentException('Timeout must be null or >= 0.');
        }

        if (self::isConcurrent()) {
            $this->runConcurrent($tasks, $onResult, $timeout);
        } else {
            $this->runSerial($tasks, $onResult, $timeout);
        }
    }

    /**
     * @param array<array-key, callable(): mixed> $tasks
     * @param callable(int|string, mixed): void $onResult
     */
    private function runSerial(array $tasks, callable $onResult, ?float $timeout): void
    {
        $startedAt = microtime(true);

        foreach ($tasks as $key => $task) {
            if ($timeout !== null && microtime(true) - $startedAt > $timeout) {
                throw new ParallelTimeoutException(sprintf('Parallel tasks exceeded %.3fs.', $timeout));
            }

            $onResult($key, $task());
        }
    }

    /**
     * @param array<array-key, callable(): mixed> $tasks
     * @param callable(int|string, mixed): void $onResult
     */
    private function runConcurrent(array $tasks, callable $onResult, ?float $timeout): void
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

        $pending = count($cids);

        while ($pending > 0) {
            $remaining = $deadline === null ? -1 : max(0.0, $deadline - microtime(true));
            $message = $channel->pop($remaining);

            if ($message === false) {
                $this->cancel($cids);
                throw new ParallelTimeoutException(
                    $timeout === null ? 'Parallel task channel closed.' : sprintf('Parallel tasks exceeded %.3fs.', $timeout)
                );
            }

            $pending--;
            unset($cids[$message['key']]);

            if (($message['error'] ?? null) instanceof Throwable) {
                $this->cancel($cids);
                throw $message['error'];
            }

            try {
                $onResult($message['key'], $message['value']);
            } catch (Throwable $throwable) {
                $this->cancel($cids);
                throw $throwable;
            }
        }
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
