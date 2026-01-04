<?php

declare(strict_types=1);

namespace FastD\Swoole\Server;

use FastD\Swoole\EventHandler\TCPEventInterface;
use FastD\Swoole\EventHandler\WebSocketEventInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

abstract class WS extends Swoole implements WebSocketEventInterface
{
    protected string $protocol = 'ws';

    public function onOpen(Server $server, Request $request): void
    {
        $server->push($request->fd, "hello, welcome\n");
    }

    public function onMessage(Server $server, Frame $frame): void
    {
        $server->push($frame->fd, "server: {$frame->data}");
    }
}
