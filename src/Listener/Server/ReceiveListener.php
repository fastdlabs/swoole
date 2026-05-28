<?php

declare(strict_types=1);

namespace FastD\Swoole\Listener\Server;

use FastD\Swoole\Event\Server\ReceiveEvent;
use FastD\Swoole\Listener\SwooleEventListener;
use Swoole\Server;

abstract class ReceiveListener extends ServerEventListener
{
    abstract public function onReceive(Server $server, int $fd, int $reactorId, string $data): void;

    public function listen(): iterable
    {
        return [
            ReceiveEvent::class,
        ];
    }
}