<?php

namespace FastD\Swoole\EventHandler;

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

interface WebSocketEventInterface extends HTTPEventInterface
{
    public function onBeforeHandshakeResponse(Request $request, Response $response): void;

    public function onHandShake(Request $request, Response $response): void;

    public function onOpen(Server $server, Request $request): void;

    public function onMessage(Server $server, Frame $frame): void;

    public function onDisconnect(Server $server, int $fd): void;
}