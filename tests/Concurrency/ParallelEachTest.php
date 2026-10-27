<?php

declare(strict_types=1);

use FastD\Swoole\Concurrency\Parallel;
use FastD\Swoole\Concurrency\ParallelTimeoutException;
use PHPUnit\Framework\TestCase;
use Swoole\Coroutine;

class ParallelEachTest extends TestCase
{
    public function testEachWithoutCallbackCollectsResults(): void
    {
        $results = Parallel::each([
            'user' => static fn (): string => 'user',
            'orders' => static fn (): string => 'orders',
        ], 0.5);

        $this->assertSame([
            'user' => 'user',
            'orders' => 'orders',
        ], $results);
    }

    public function testEachWithCallbackReturnsEmptyArray(): void
    {
        $seen = [];

        $result = Parallel::each([
            'user' => static fn (): string => 'user',
            'orders' => static fn (): string => 'orders',
        ], 0.5, static function (int|string $key, mixed $value) use (&$seen): void {
            $seen[$key] = $value;
        });

        $this->assertSame([], $result);
        $this->assertSame([
            'user' => 'user',
            'orders' => 'orders',
        ], $seen);
    }

    public function testEachAcceptsInvokableCallback(): void
    {
        $callback = new class {
            /** @var array<array-key, mixed> */
            public array $seen = [];

            public function __invoke(int|string $key, mixed $value): void
            {
                $this->seen[$key] = $value;
            }
        };

        Parallel::each([
            'one' => static fn (): int => 1,
        ], 0.5, $callback);

        $this->assertSame(['one' => 1], $callback->seen);
    }

    public function testEachEmptyTasksReturnsEmptyArray(): void
    {
        $this->assertSame([], Parallel::each([], 0.5));
        $this->assertSame([], Parallel::each([], 0.5, static function (): void {}));
    }

    public function testEachCallbackRunsInsideCoroutine(): void
    {
        $seen = null;
        $result = null;

        Coroutine\run(static function () use (&$seen, &$result): void {
            $result = Parallel::each([
                'a' => static function (): string {
                    Coroutine::sleep(0.02);
                    return 'a';
                },
                'b' => static function (): string {
                    Coroutine::sleep(0.01);
                    return 'b';
                },
            ], 0.5, static function (int|string $key, mixed $value) use (&$seen): void {
                $seen[$key] = $value;
            });
        });

        $this->assertSame([], $result);
        ksort($seen);
        $this->assertSame(['a' => 'a', 'b' => 'b'], $seen);
    }

    public function testEachTimeoutIsEnforced(): void
    {
        $caught = null;
        $seen = [];

        Coroutine\run(function () use (&$caught, &$seen): void {
            try {
                Parallel::each([
                    'slow' => static function (): void {
                        Coroutine::sleep(0.2);
                    },
                ], 0.02, static function (int|string $key, mixed $value) use (&$seen): void {
                    $seen[$key] = $value;
                });
            } catch (Throwable $throwable) {
                $caught = $throwable;
            }
        });

        $this->assertInstanceOf(ParallelTimeoutException::class, $caught);
        $this->assertSame([], $seen);
    }

    public function testEachCallbackExceptionIsPropagated(): void
    {
        $caught = null;

        Coroutine\run(function () use (&$caught): void {
            try {
                Parallel::each([
                    'task' => static fn (): string => 'value',
                ], 0.5, static function (): void {
                    throw new RuntimeException('callback failed');
                });
            } catch (Throwable $throwable) {
                $caught = $throwable;
            }
        });

        $this->assertInstanceOf(RuntimeException::class, $caught);
        $this->assertSame('callback failed', $caught->getMessage());
    }
}
