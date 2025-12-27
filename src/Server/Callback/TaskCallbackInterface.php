<?php

declare(strict_types=1);

namespace FastD\Swoole\Server\Callback;

use Swoole\Server;

interface TaskCallbackInterface extends TCPCallbackInterface
{
    const CALLBACK = [
        'onStart',
        'onShutdown',
        'onManagerStart',
        'onManagerStop',
        'onWorkerStart',
        'onWorkerStop',
        'onWorkerError',
        'onWorkerExit',
        'onClose',
        'onConnect',
        'onReceive',
        'onTask',
        'onFinish',
    ];

    public function onTask(Server $server, int $taskId, int $workerId, string $data):  void;

    public function onFinish(Server $server, int $taskId, string $data): void;
}
