<?php

declare(strict_types=1);

use FastD\Swoole\Process;
use FastD\Swoole\Process\Worker;

require_once __DIR__ . '/../../vendor/autoload.php';

final class LoopWorker extends Worker
{
    public function process(Worker $worker): void
    {
        echo sprintf('[worker %s] started pid=%d', $worker->name, getmypid()) . PHP_EOL;

        while (true) {
            sleep(1);
        }
    }
}

$manager = new Process();

$manager->addWorker(
    new LoopWorker('worker-a'),
    new LoopWorker('worker-b')
);

echo 'manager starting...' . PHP_EOL;

$manager->start();

// start() 在非 daemon 模式会等待 manager 退出
echo 'manager stopped' . PHP_EOL;
