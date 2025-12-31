<?php

declare(strict_types=1);

namespace FastD\Swoole\EventHandler;

use Swoole\Server;

interface WorkerEventInterface
{
    public function onStart(Server $server): void;

    public function onBeforeShutdown(Server $server):void;

    public function onShutdown(Server $server): void;

    public function onManagerStart(Server $server): void;

    public function onManagerStop(Server $server): void;

    public function onWorkerStart(Server $server, int $id): void;

    public function onWorkerStop(Server $server, int $id): void;

    public function onWorkerError(Server $server, int $id, int $workerPid, int $exitCode, int $signal): void;

    public function onWorkerExit(Server $server, int $id): void;

    public function onBeforeReload(Server $server): void;

    public function onAfterReload(Server $server): void;
}