<?php

declare(strict_types=1);

namespace FastD\Swoole\Server\Callback;

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server;

interface HTTPCallbackInterface extends TCPCallbackInterface
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
    ];

    public function onRequest(Request $request, Response $response): void;
}
