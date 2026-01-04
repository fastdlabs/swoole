<?php

use FastD\Http\Request\ServerRequest;
use FastD\Http\Response\Response;
use FastD\Swoole\Server\HTTP;
use Swoole\Server;

include __DIR__ . '/../../vendor/autoload.php';

$server = new class extends HTTP
{
    public function onResponse(ServerRequest $serverRequest): Response
    {
        $queryParams = $serverRequest->getQueryParams();
        $name = $queryParams['name'] ?? 'world';
        return new Response("hello {$name}");
    }
};

$server->start();
