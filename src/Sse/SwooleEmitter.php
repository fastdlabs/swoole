<?php

declare(strict_types=1);

namespace FastD\Swoole\Sse;

use FastD\Http\Response\SseResponse;
use FastD\Http\Sse\EmitterInterface;
use FastD\Http\Sse\Event;
use RuntimeException;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use Swoole\Http\Response;
use Throwable;

/**
 * Swoole 运行时的 SSE 发送器。
 *
 * 生产者运行在独立协程中，帧经 Channel 交给主循环写出；主循环以心跳间隔
 * 作为 pop 超时，因此生产者阻塞（等待上游 AI 响应）时仍能持续发送心跳，
 * 同时能及时发现客户端断开并取消生产者协程。
 */
final class SwooleEmitter implements EmitterInterface
{
    private const CHANNEL_SIZE = 64;

    private const WRITE_RETRY = 100;

    private Channel $channel;

    private bool $closed = false;

    /** @var \Closure(string): bool */
    private \Closure $writer;

    /** @var \Closure(): bool */
    private \Closure $writable;

    /** @var \Closure(float): void */
    private \Closure $sleep;

    /** @var \Closure(): void */
    private \Closure $end;

    /**
     * 注入 writer/writable/sleep 便于单元测试（默认走 Swoole\Http\Response）。
     *
     * @param \Closure(string): bool|null $writer
     * @param \Closure(): bool|null       $writable
     * @param \Closure(float): void|null  $sleep
     * @param \Closure(): void|null       $end
     */
    public function __construct(
        private ?Response $response = null,
        ?\Closure $writer = null,
        ?\Closure $writable = null,
        ?\Closure $sleep = null,
        ?\Closure $end = null,
    ) {
        $this->channel = new Channel(self::CHANNEL_SIZE);

        $this->writer = $writer ?? static fn (string $frame): bool => $response->write($frame);
        $this->writable = $writable ?? static fn (): bool => $response->isWritable();
        $this->sleep = $sleep ?? static function (float $seconds): void {
            Coroutine::sleep($seconds);
        };
        $this->end = $end ?? static function () use ($response): void {
            if ($response === null) {
                return;
            }

            try {
                $response->end();
            } catch (Throwable) {
                // 连接已断开
            }
        };
    }

    public static function fromResponse(Response $response): self
    {
        return new self($response);
    }

    public function send(Event|string|array $event): void
    {
        $this->push($this->normalize($event)->format());
    }

    public function comment(string $text): void
    {
        $this->push(': ' . str_replace(["\r", "\n"], ' ', $text) . "\n\n");
    }

    public function retry(int $milliseconds): void
    {
        if ($milliseconds < 0) {
            return;
        }

        $this->push('retry: ' . $milliseconds . "\n\n");
    }

    public function isClosed(): bool
    {
        return $this->closed;
    }

    public function close(): void
    {
        $this->closed = true;
    }

    /**
     * 执行生产者并写出 SSE 流（阻塞至结束）。
     */
    public function run(SseResponse $response): void
    {
        if (Coroutine::getCid() <= 0) {
            throw new RuntimeException(
                'Swoole SSE requires a coroutine context. Set "enable_coroutine" => true on the Swoole server.'
            );
        }

        $heartbeat = $response->getHeartbeat();
        $maxDuration = $response->getMaxDuration();
        $deadline = $maxDuration > 0 ? microtime(true) + $maxDuration : null;
        $producer = $response->getProducer();

        if ($response->getRetry() !== null) {
            $this->writeRaw('retry: ' . $response->getRetry() . "\n\n");
        }

        $producerCid = Coroutine::create(function () use ($producer): void {
            try {
                $producer($this);
                $this->pushControl(['done' => true]);
            } catch (Throwable $e) {
                $this->pushControl(['error' => $e]);
            }
        });

        if ($producerCid === false) {
            throw new RuntimeException('Unable to create the SSE producer coroutine.');
        }

        try {
            while (true) {
                $timeout = $heartbeat > 0 ? $heartbeat : 0.5;
                $frame = $this->channel->pop($timeout);

                if ($frame === false) {
                    if ($this->reachedDeadline($deadline)) {
                        break;
                    }

                    if ($heartbeat > 0 && !$this->writeRaw(": ping\n\n")) {
                        break;
                    }

                    if (!Coroutine::exists($producerCid) && $this->channel->isEmpty()) {
                        break;
                    }

                    continue;
                }

                if (!is_string($frame)) {
                    if (isset($frame['error']) && $frame['error'] instanceof Throwable) {
                        $this->handleProducerError($frame['error'], $response);
                    }

                    break;
                }

                if (!$this->writeRaw($frame)) {
                    break;
                }

                if ($this->reachedDeadline($deadline)) {
                    break;
                }
            }
        } catch (Throwable $e) {
            // 写帧/协程异常：尽力下发错误事件，避免抛出到 RequestListener 造成二次响应
            try {
                $this->handleProducerError($e, $response);
            } catch (Throwable) {
                // 忽略二次异常
            }
        } finally {
            $this->closed = true;

            if (Coroutine::exists($producerCid)) {
                try {
                    Coroutine::cancel($producerCid);
                } catch (Throwable) {
                    // 生产者已退出
                }
            }

            try {
                $this->channel->close();
            } catch (Throwable) {
                // channel 已关闭
            }

            // 关键：结束响应，否则 Swoole 连接会一直保持打开
            ($this->end)();
        }
    }

    private function push(string $frame): void
    {
        if ($this->closed) {
            return;
        }

        $this->pushControl($frame);
    }

    private function pushControl(mixed $value): void
    {
        try {
            $this->channel->push($value);
        } catch (Throwable) {
            // channel 已关闭（客户端断开/流结束）
            $this->closed = true;
        }
    }

    private function handleProducerError(Throwable $e, SseResponse $response): void
    {
        $response->callErrorHandler($e);

        $frame = $response->errorFrame($e);

        if ($frame !== null) {
            $this->writeRaw($frame);
        }
    }

    private function writeRaw(string $frame): bool
    {
        if ($this->closed) {
            return false;
        }

        for ($attempt = 0; $attempt < self::WRITE_RETRY; $attempt++) {
            if (!($this->writable)()) {
                $this->closed = true;

                return false;
            }

            if (($this->writer)($frame)) {
                return true;
            }

            ($this->sleep)(0.005);
        }

        $this->closed = true;

        return false;
    }

    private function reachedDeadline(?float $deadline): bool
    {
        return $deadline !== null && microtime(true) >= $deadline;
    }

    private function normalize(Event|string|array $event): Event
    {
        return $event instanceof Event ? $event : Event::data($event);
    }
}
