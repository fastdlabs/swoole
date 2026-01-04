<?php

use FastD\Http\Request\ServerRequest;
use FastD\Http\Response\Response;
use FastD\Swoole\EventHandler\TaskEventInterface;
use FastD\Swoole\Server\HTTP;
use Swoole\Server;

include __DIR__ . '/../../vendor/autoload.php';

$server = new class extends HTTP implements TaskEventInterface
{
    public function onResponse(ServerRequest $serverRequest): Response
    {
        return new Response("hello {$serverRequest->getQueryParams()['name']}");
    }

    public function onTask(Server $server, int $taskId, int $srcWorkerId, mixed $data): void
    {
        // TODO: Implement onTask() method.
    }

    public function onFinish(Server $server, int $taskId, mixed $data): void
    {
        // TODO: Implement onFinish() method.
    }

    public function onPipeMessage(Server $server, int $srcWorkerId, mixed $message): void
    {
        // TODO: Implement onPipeMessage() method.
    }
};

$server->start();
