<?php

declare(strict_types=1);

namespace FastD\Swoole\Server;

use Exception;
use FastD\Http\Exception\HttpException;
use FastD\Http\Request\ServerRequest;
use FastD\Http\Request\SwooleServerRequest;
use FastD\Swoole\EventHandler\HTTPEventInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server as HTTPServer;
use Swoole\Server;
use Throwable;

abstract class HTTP extends Swoole implements HTTPEventInterface
{
    const Ignore = '/favicon.ico';

    public function onRequest(Request $request, Response $response): void
    {
        try {
            if (HTTP::Ignore == $request->server['path_info'] || HTTP::Ignore == $request->server['request_uri']) {
                $response->end();
                return;
            }
            $serverRequest = SwooleServerRequest::createServerRequestFromSwoole($request);
            $this->sendResponse($response, $this->onResponse($serverRequest));
        } catch (Throwable $e) {
            $this->sendResponse($response, $this->onException($e));
        }
    }

    abstract public function onResponse(ServerRequest $serverRequest): \FastD\Http\Response\Response;

    public function onException(Throwable $throwable): \FastD\Http\Response\Response
    {
        $errorData = [
            'type' => get_class($throwable),
            'message' => $throwable->getMessage(),
            'code' => $throwable->getCode(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'trace' => explode("\n", $throwable->getTraceAsString())
        ];

        return new \FastD\Http\Response\JsonResponse($errorData, $throwable instanceof HttpException ? $throwable->getStatusCode() : 500);
    }

    protected function sendResponse(Response $swooleResponse, \FastD\Http\Response\Response $response): void
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
