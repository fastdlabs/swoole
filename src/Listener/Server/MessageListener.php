<?php

declare(strict_types=1);

namespace FastD\Swoole\Listener\Server;

use FastD\Swoole\Event\Server\MessageEvent;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

abstract class MessageListener extends ServerEventListener
{
    abstract public function onOpen(Server $server, Request $request): void;

    abstract public function onMessage(Server $server, Frame $frame): void;

    public function listen(): iterable
    {
        return [
            MessageEvent::class
        ];
    }
}