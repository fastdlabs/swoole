<?php

use FastD\Swoole\Server\HTTP;
use Swoole\Server;

include __DIR__ . '/../vendor/autoload.php';

$server = new class extends HTTP
{
    public function handleRequest(\FastD\Http\Request\ServerRequest $serverRequest): \FastD\Http\Response\Response
    {
        return new \FastD\Http\Response\Response("hello {$serverRequest->getQueryParams()['name']}");
    }

    public function onStart(Server $server): void
    {
        echo "server start on {$this->protocol}://{$this->host}:{$this->port}\n";
    }

    public function onBeforeShutdown(Server $server): void
    {
        // TODO: Implement onBeforeShutdown() method.
    }

    public function onShutdown(Server $server): void
    {
        // TODO: Implement onShutdown() method.
    }

    public function onManagerStart(Server $server): void
    {
        // TODO: Implement onManagerStart() method.
    }

    public function onManagerStop(Server $server): void
    {
        // TODO: Implement onManagerStop() method.
    }

    public function onWorkerStart(Server $server, int $id): void
    {
        // TODO: Implement onWorkerStart() method.
    }

    public function onWorkerStop(Server $server, int $id): void
    {
        // TODO: Implement onWorkerStop() method.
    }

    public function onWorkerError(Server $server, int $id, int $workerPid, int $exitCode, int $signal): void
    {
        // TODO: Implement onWorkerError() method.
    }

    public function onWorkerExit(Server $server, int $id): void
    {
        // TODO: Implement onWorkerExit() method.
    }

    public function onBeforeReload(Server $server): void
    {
        // TODO: Implement onBeforeReload() method.
    }

    public function onAfterReload(Server $server): void
    {
        // TODO: Implement onAfterReload() method.
    }
};

$server->start();
