<?php

declare(strict_types=1);

namespace FastD\Swoole\Server\Listener;

use FastD\Event\EventListenerInterface;
use FastD\Swoole\Server\Event\MessageEvent;
use FastD\Swoole\Server\WS;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

abstract class MessageListener extends RequestListener
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