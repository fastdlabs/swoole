<?php

declare(strict_types=1);

/**
 * Parallel 运行时自适应示例。
 *
 * CGI / 普通 CLI：
 *   php examples/concurrency/parallel.php
 *
 * Swoole 协程：
 *   php examples/concurrency/parallel.php --swoole
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
    $result = Parallel::all($tasks, 0.5);

    echo 'concurrent: ' . (Parallel::isConcurrent() ? 'true' : 'false') . PHP_EOL;
    echo 'elapsed_ms: ' . round((microtime(true) - $startedAt) * 1000, 2) . PHP_EOL;

    foreach ($result as $name => $item) {
        echo sprintf('%s: %s (%dms)', $name, $item['status'], $item['delay_ms']) . PHP_EOL;
    }
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
