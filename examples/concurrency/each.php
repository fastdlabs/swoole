<?php

declare(strict_types=1);

/**
 * Parallel::each() 流式回调示例。
 *
 * CGI / 普通 CLI：
 *   php examples/concurrency/each.php
 *
 * Swoole 协程：
 *   php examples/concurrency/each.php --swoole
 */

use FastD\Swoole\Concurrency\Parallel;
use Swoole\Coroutine;

require_once __DIR__ . '/../../vendor/autoload.php';

$tasks = [
    'user' => static fn (): array => waitThen('user', 0.08),
    'orders' => static fn (): array => waitThen('orders', 0.12),
    'notifications' => static fn (): array => waitThen('notifications', 0.06),
];

$run = static function () use ($tasks): void {
    $startedAt = microtime(true);

    $return = Parallel::each(
        $tasks,
        0.5,
        static function (int|string $key, mixed $value): void {
            echo sprintf('%s: %s (%dms)', $key, $value['status'], $value['delay_ms']) . PHP_EOL;
        }
    );

    echo 'concurrent: ' . (Parallel::isConcurrent() ? 'true' : 'false') . PHP_EOL;
    echo 'returned: ' . json_encode($return) . PHP_EOL;
    echo 'elapsed_ms: ' . round((microtime(true) - $startedAt) * 1000, 2) . PHP_EOL;
};

if (in_array('--swoole', $argv, true) && extension_loaded('swoole')) {
    Coroutine\run($run);
} else {
    $run();
}

function waitThen(string $name, float $seconds): array
{
    if (Parallel::isConcurrent()) {
        Coroutine::sleep($seconds);
    } else {
        usleep((int) ($seconds * 1_000_000));
    }

    return [
        'service' => $name,
        'status' => 'ok',
        'delay_ms' => (int) ($seconds * 1000),
    ];
}
