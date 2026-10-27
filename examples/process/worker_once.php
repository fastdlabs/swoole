<?php

declare(strict_types=1);

use FastD\Swoole\Process\Worker;

require_once __DIR__ . '/../../vendor/autoload.php';

final class OnceWorker extends Worker
{
    public function process(Worker $worker): void
    {
        echo sprintf('[worker] pid=%d name=%s', getmypid(), $worker->name) . PHP_EOL;

        for ($i = 1; $i <= 3; $i++) {
            echo sprintf('[worker] step %d/3', $i) . PHP_EOL;
            sleep(1);
        }
    }
}

$worker = new OnceWorker('once-worker');

echo 'pid before start: ' . $worker->getPid() . PHP_EOL;
echo 'starting worker...' . PHP_EOL;

$pid = $worker->start();
echo sprintf('worker started: %d, parent: %d', $pid, getmypid()) . PHP_EOL;

// 父进程阻塞等待子进程结束并回收
$ret = \Swoole\Process::wait(true);
if (is_array($ret)) {
    echo sprintf(
        '[parent] reaped pid=%d code=%d signal=%d',
        $ret['pid'],
        $ret['code'],
        $ret['signal']
    ) . PHP_EOL;
}

$worker->stop();
