<?php

declare(strict_types=1);

namespace FastD\Swoole\EventHandler;

use Swoole\Http\Request;
use Swoole\Http\Response;

interface HTTPEventInterface
{
    public function onRequest(Request $request, Response $response): void;
}