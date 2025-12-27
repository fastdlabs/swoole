<?php

declare(strict_types=1);

namespace FastD\Swoole\Server\Callback;

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

interface WebSocketCallbackInterface extends HTTPCallbackInterface
{
    const CALLBACK = [
        'onStart',
        'onShutdown',
        'onManagerStart',
        'onManagerStop',
        'onWorkerStart',
        'onWorkerStop',
        'onWorkerError',
        'onWorkerExit',
        'onClose',
        'onRequest',
        'onHandShake',
        'onOpen',
        'onMessage',
        'onDisconnect',
    ];

    public function onHandShake(Request $request, Response $response): void;

    public function onOpen(Server $server, Request $request): void;

    public function onMessage(Server $server, Frame $frame): void ;

    public function onDisconnect(Server $server, int $fd): void;
}
