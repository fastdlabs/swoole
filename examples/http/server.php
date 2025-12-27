<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

use FastD\Http\Response;
use FastD\Http\ServerRequest;
use FastD\Swoole\Server\HTTP;
use FastD\Swoole\Server\LIstener\TCPListener;

include __DIR__ . '/../../vendor/autoload.php';

class HttpHandler extends TCPListener
{
    /**
     * @param ServerRequest $serverRequest
     * @return Response
     */
    public function handleRequest(ServerRequest $serverRequest): Response
    {
        return new Response("hello");
    }
};

$server = new HTTP();
$server->handle(HttpHandler::class);

$server->start();
