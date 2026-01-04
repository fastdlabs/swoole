<?php

declare(strict_types=1);

namespace FastD\Swoole\EventHandler;

use Swoole\Server;

interface TaskEventInterface
{
    public function onTask(Server $server, int $taskId, int $srcWorkerId, mixed $data): void;

    public function onFinish(Server $server, int $taskId, mixed $data): void;

    public function onPipeMessage(Server $server, int $srcWorkerId, mixed $message): void;
}