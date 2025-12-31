<?php

declare(strict_types=1);

namespace FastD\Swoole\Server;

use FastD\Http\Exception\HttpException;
use FastD\Http\Request\ServerRequest;
use FastD\Http\Request\SwooleServerRequest;
use FastD\Swoole\EventHandler\HTTPEventInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server as HTTPServer;
use Swoole\Server;

abstract class HTTP extends Swoole implements HTTPEventInterface
{
    protected string $protocol = 'http';

    public function createSwooleServer(string $protocol, string $host, int $port, int $mode, int $sockType): Server
    {
        return new HTTPServer($host, $port, $mode, $sockType);
    }

    public function onRequest(Request $request, Response $response): void
    {
        try {
            $serverRequest = SwooleServerRequest::createServerRequestFromSwoole($request);
            $this->handleResponse($response, $this->handleRequest($serverRequest, $response));
        } catch (HttpException $e) {
            $this->handleResponse($response, new \FastD\Http\Response\Response($e->getMessage(), $e->getStatusCode()));
        } catch (\Exception $e){
            $this->handleResponse($response, new \FastD\Http\Response\Response($e->getMessage(), 500));
        }
    }

    abstract public function handleRequest(ServerRequest $serverRequest): \FastD\Http\Response\Response;

    protected function handleResponse(Response $swooleResponse, \FastD\Http\Response\Response $response): void
    {
        $this->sendHeaders($swooleResponse, $response);
        $swooleResponse->status($response->getStatusCode());
        $swooleResponse->end((string) $response->getBody());
    }

    protected function sendHeaders(Response $swooleResponse, \FastD\Http\Response\Response $response): void
    {
        foreach ($response->getHeaders() as $key => $header) {
            $swooleResponse->header($key, $response->getHeaderLine($key));
        }

        foreach ($response->getCookies() as $key => $cookieParam) {
            $swooleResponse->cookie($key, (string) $cookieParam);
        }
    }
}
