<?php

declare(strict_types=1);

use FastD\Swoole\Concurrency\Parallel;
use FastD\Swoole\Concurrency\ParallelTimeoutException;
use PHPUnit\Framework\TestCase;
use Swoole\Coroutine;

class ParallelTest extends TestCase
{
    public function testSerialFallbackOutsideCoroutine(): void
    {
        $this->assertFalse(Parallel::isConcurrent());

        $order = [];
        $results = Parallel::all([
            'first' => static function () use (&$order): string {
                $order[] = 'first';
                return 'a';
            },
            'second' => static function () use (&$order): string {
                $order[] = 'second';
                return 'b';
            },
        ]);

        $this->assertSame(['first', 'second'], $order);
        $this->assertSame(['first' => 'a', 'second' => 'b'], $results);
    }

    public function testConcurrentExecutionInsideCoroutine(): void
    {
        $elapsed = null;
        $results = null;

        Coroutine\run(static function () use (&$elapsed, &$results): void {
            $startedAt = microtime(true);
            $results = Parallel::all([
                'a' => static fn (): string => (static function (): string {
                    Coroutine::sleep(0.05);
                    return 'a';
                })(),
                'b' => static fn (): string => (static function (): string {
                    Coroutine::sleep(0.05);
                    return 'b';
                })(),
                'c' => static fn (): string => (static function (): string {
                    Coroutine::sleep(0.05);
                    return 'c';
                })(),
            ]);
            $elapsed = microtime(true) - $startedAt;

            self::assertTrue(Parallel::isConcurrent());
        });

        $this->assertSame(['a' => 'a', 'b' => 'b', 'c' => 'c'], $results);
        $this->assertLessThan(0.13, $elapsed);
    }

    public function testTaskExceptionIsPropagated(): void
    {
        $caught = null;

        Coroutine\run(function () use (&$caught): void {
            try {
                Parallel::all([
                    'ok' => static fn (): string => 'ok',
                    'fail' => static fn (): string => throw new RuntimeException('task failed'),
                ]);
            } catch (Throwable $throwable) {
                $caught = $throwable;
            }
        });

        $this->assertInstanceOf(RuntimeException::class, $caught);
        $this->assertSame('task failed', $caught->getMessage());
    }

    public function testTimeoutIsEnforced(): void
    {
        $caught = null;

        Coroutine\run(function () use (&$caught): void {
            try {
                Parallel::all([
                    'slow' => static function (): void {
                        Coroutine::sleep(0.2);
                    },
                ], 0.02);
            } catch (Throwable $throwable) {
                $caught = $throwable;
            }
        });

        $this->assertInstanceOf(ParallelTimeoutException::class, $caught);
    }

    public function testEmptyTasksReturnsEmptyArray(): void
    {
        $this->assertSame([], Parallel::all([]));
    }

    public function testNegativeTimeoutIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Parallel::all([
            'task' => static fn (): string => 'ok',
        ], -0.1);
    }

    public function testSerialTaskExceptionIsPropagated(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('serial task failed');

        Parallel::all([
            'fail' => static fn (): string => throw new RuntimeException('serial task failed'),
        ]);
    }

    public function testSerialTimeoutIsEnforcedBetweenTasks(): void
    {
        $this->expectException(ParallelTimeoutException::class);

        Parallel::all([
            'first' => static function (): void {
                usleep(30_000);
            },
            'second' => static fn (): string => 'never',
        ], 0.01);
    }
}
