<?php

declare(strict_types=1);

namespace FastD\Swoole\Server\Callback;

use Swoole\Server;

interface UDPCallbackInterface extends CallbackInterface
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
        'onPacket',
    ];

    public function onClose(Server $server, int $fd, int $reactorId): void;

    public function onPacket(Server $server, string $data, array $clientInfo): void;
}
