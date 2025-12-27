<?php

declare(strict_types=1);

namespace FastD\Swoole\Server\Callback;

use Swoole\Server;

interface TCPCallbackInterface extends CallbackInterface
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
        'onConnect',
        'onReceive',
    ];

    public function onClose(Server $server, int $fd, int $reactorId): void;

    public function onConnect(Server $server, int $fd, int $reactorId): void;

    public function onReceive(Server $server, int $fd, int $reactorId, string $data): bool;
}
