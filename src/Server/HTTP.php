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

abstract class HTTP extends Swoole implements HTTPEventInterface
{
    public function onRequest(Request $request, Response $response): void
    {
        try {
            if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
                $response->end();
                return;
            }
            $serverRequest = SwooleServerRequest::createServerRequestFromSwoole($request);
            $this->sendResponse($response, $this->onResponse($serverRequest));
        } catch (HttpException|Exception $e) {
            $this->sendResponse($response, $this->onException($e));
        }
    }

    abstract public function onResponse(ServerRequest $serverRequest): \FastD\Http\Response\Response;

    public function onException(Exception $exception): \FastD\Http\Response\Response
    {
        $errorMessage = "[Exception] " . $exception->getMessage() .
            " in " . $exception->getFile() .
            " at line " . $exception->getLine() .
            "\nStack trace:\n" . $exception->getTraceAsString();

        return new \FastD\Http\Response\Response($errorMessage, $exception instanceof HttpException ? $exception->getStatusCode() : 500);
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
