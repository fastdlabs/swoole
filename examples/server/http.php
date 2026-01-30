<?php

use FastD\Http\Request\ServerRequest;
use FastD\Http\Response\Text as Response;
use FastD\Swoole\Server\EventListener\HTTPListener;
use FastD\Swoole\Server\HTTP;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

include __DIR__ . '/../../vendor/autoload.php';

$server = new class extends HTTP {
    public function onRequest(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(200, 'hello powered by swoole');
    }
};

$server->addEventListener(new HTTPListener());

$server->start();
