<?php

declare(strict_types=1);

namespace FastD\Swoole\Listener\Server;

use FastD\Http\Response\Json;
use FastD\Swoole\Server;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Status;

class StatsListener extends RequestListener
{
    protected Server $server;
    public function process(object $event): void
    {
        $this->server = $event->object;
        parent::process($event);
    }

    public function onRequest(ServerRequestInterface $serverRequest): ResponseInterface
    {
        return new Json(Status::OK, $this->server->swoole->stats());
    }
}