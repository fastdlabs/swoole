<?php

declare(strict_types=1);

namespace FastD\Swoole\Listener\Server;

use FastD\Http\Request\SwooleServerRequest;
use FastD\Http\Response\Json;
use FastD\Swoole\Event\Server\RequestEvent;
use FastD\Swoole\Event\SwooleEvent;
use FastD\Swoole\Listener\SwooleEventListener;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Response;
use Throwable;

abstract class RequestListener extends ServerEventListener
{
    const Ignore = '/favicon.ico';

    public function listen(): iterable
    {
        return [
            RequestEvent::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof SwooleEvent && $event->event == 'request') {
            try {
                [$request, $response] = $event->args;
                if (static::Ignore == $request->server['path_info'] || static::Ignore == $request->server['request_uri']) {
                    $response->end();
                    return;
                }
                if ($event->object->swoole->getClientInfo($request->fd)['server_port'] == $this->port) {
                    $serverRequest = SwooleServerRequest::fromSwoole($request);
                    $this->sendResponse($response, $this->onRequest($serverRequest));
                }
            } catch (Throwable $e) {
                $this->sendResponse($response, $this->onException($e));
            }
        }
    }

    abstract public function onRequest(ServerRequestInterface $serverRequest): ResponseInterface;

    public function onException(Throwable $e): ResponseInterface
    {
        return new Json(500, [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => explode("\n", $e->getTraceAsString()),
        ]);
    }

    protected function sendResponse(Response $swooleResponse, ResponseInterface $response): void
    {
        // send headers
        foreach ($response->getHeaders() as $key => $header) {
            $swooleResponse->header($key, $response->getHeaderLine($key));
        }

        foreach ($response->getCookies() as $key => $cookieParam) {
            $swooleResponse->cookie($key, (string) $cookieParam);
        }

        // send contents
        $swooleResponse->status($response->getStatusCode());
        $swooleResponse->end((string) $response->getBody());
    }
}