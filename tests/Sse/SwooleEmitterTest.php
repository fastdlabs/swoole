<?php

declare(strict_types=1);

use FastD\Http\Response\SseResponse;
use FastD\Http\Sse\EmitterInterface;
use FastD\Swoole\Sse\SwooleEmitter;
use PHPUnit\Framework\TestCase;
use Swoole\Coroutine;

/**
 * Swoole SSE 发送器测试（真实协程 + 注入 writer，不启动 HTTP 服务）。
 */
class SwooleEmitterTest extends TestCase
{
    /** @var list<string> */
    private array $frames = [];

    private function emitter(?\Closure $writable = null, ?\Closure $sleep = null): SwooleEmitter
    {
        $this->frames = [];

        return new SwooleEmitter(
            null,
            function (string $frame): bool {
                $this->frames[] = $frame;

                return true;
            },
            $writable ?? static fn (): bool => true,
            $sleep ?? static function (float $seconds): void {
                Coroutine::sleep($seconds);
            },
        );
    }

    public function testStreamsProducerFrames(): void
    {
        $emitter = $this->emitter();
        $response = new SseResponse(function (EmitterInterface $emitter): void {
            $emitter->send('hello');
            $emitter->send(['name' => 'fastd']);
            $emitter->comment('ping');
        }, ['heartbeat' => 0]);

        Coroutine\run(function () use ($emitter, $response): void {
            $emitter->run($response);
        });

        $this->assertSame([
            "data: hello\n\n",
            "data: {\"name\":\"fastd\"}\n\n",
            ": ping\n\n",
        ], $this->frames);
    }

    public function testRetryFrameIsWrittenFirst(): void
    {
        $emitter = $this->emitter();
        $response = new SseResponse(function (EmitterInterface $emitter): void {
            $emitter->send('x');
        }, ['heartbeat' => 0, 'retry' => 3000]);

        Coroutine\run(function () use ($emitter, $response): void {
            $emitter->run($response);
        });

        $this->assertSame("retry: 3000\n\n", $this->frames[0]);
        $this->assertSame("data: x\n\n", $this->frames[1]);
    }

    public function testHeartbeatIsSentWhileProducerWaits(): void
    {
        $emitter = $this->emitter();
        $response = new SseResponse(function (EmitterInterface $emitter): void {
            Coroutine::sleep(0.15);
            $emitter->send('done');
        }, ['heartbeat' => 0.05]);

        Coroutine\run(function () use ($emitter, $response): void {
            $emitter->run($response);
        });

        $heartbeats = array_filter($this->frames, static fn (string $frame): bool => $frame === ": ping\n\n");

        $this->assertNotEmpty($heartbeats);
        $this->assertSame("data: done\n\n", end($this->frames));
    }

    public function testProducerExceptionBecomesErrorEvent(): void
    {
        $logged = null;
        $emitter = $this->emitter();
        $response = new SseResponse(function (): void {
            throw new RuntimeException('boom', 500);
        }, [
            'heartbeat' => 0,
            'on_error' => function (Throwable $e) use (&$logged): void {
                $logged = $e;
            },
        ]);

        Coroutine\run(function () use ($emitter, $response): void {
            $emitter->run($response);
        });

        $this->assertInstanceOf(RuntimeException::class, $logged);
        $this->assertCount(1, $this->frames);
        $this->assertStringContainsString("event: error\n", $this->frames[0]);
        $this->assertStringContainsString('"message":"boom"', $this->frames[0]);
        $this->assertTrue($emitter->isClosed());
    }

    public function testMaxDurationEndsTheStream(): void
    {
        $emitter = $this->emitter();
        $response = new SseResponse(function (EmitterInterface $emitter): void {
            Coroutine::sleep(1.0);
            $emitter->send('too late');
        }, ['heartbeat' => 0.02, 'max_duration' => 0.05]);

        $started = microtime(true);

        Coroutine\run(function () use ($emitter, $response): void {
            $emitter->run($response);
        });

        $this->assertLessThan(0.5, microtime(true) - $started);
        $this->assertNotContains("data: too late\n\n", $this->frames);
    }

    public function testClientDisconnectStopsWriting(): void
    {
        $emitter = $this->emitter(static fn (): bool => false);
        $response = new SseResponse(function (EmitterInterface $emitter): void {
            $emitter->send('a');
        }, ['heartbeat' => 0]);

        Coroutine\run(function () use ($emitter, $response): void {
            $emitter->run($response);
        });

        $this->assertSame([], $this->frames);
        $this->assertTrue($emitter->isClosed());
    }

    public function testRunOutsideCoroutineThrows(): void
    {
        $emitter = $this->emitter();
        $response = new SseResponse([], ['heartbeat' => 0]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('coroutine');

        $emitter->run($response);
    }
}
