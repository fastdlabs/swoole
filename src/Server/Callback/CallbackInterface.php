<?php

namespace FastD\Swoole\Server\Callback;

use Swoole\Server;

interface CallbackInterface
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
    ];

    public function onStart(Server $server): void;

    public function onShutdown(Server $server): void;

    public function onManagerStart(Server $server): void;

    public function onManagerStop(Server $server): void;

    public function onWorkerStart(Server $server, int $id): void;

    public function onWorkerStop(Server $server, int $id): void;

    public function onWorkerError(Server $server, int $id, int $workerPid, int $exitCode, int $signal): void;

    public function onWorkerExit(Server $server, int $id): void;
}