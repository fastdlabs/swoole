<?php

declare(strict_types=1);

namespace FastD\Swoole\Server\Listener;

use FastD\Swoole\Server\Event\ReceiveEvent;
use Swoole\Server;

abstract class ReceiveListener extends SwooleEventListener
{
    abstract public function onReceive(Server $server, int $fd, int $reactorId, string $data): void;

    public function listen(): iterable
    {
        return [
            ReceiveEvent::class,
        ];
    }
}