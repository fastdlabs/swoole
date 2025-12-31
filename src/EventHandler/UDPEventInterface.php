<?php

declare(strict_types=1);

namespace FastD\Swoole\EventHandler;

use Swoole\Server;

interface UDPEventInterface
{
    public function onReceive(Server $server, int $fd, int $reactorId, string $data);

    public function onTask(Server $server, int $taskId, int $srcWorkerId, mixed $data);

    public function onFinish(Server $server, int $taskId, mixed $data);

    public function onPipeMessage(Server $server, int $srcWorkerId, mixed $message);
}