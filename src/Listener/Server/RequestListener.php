<?php

declare(strict_types=1);

namespace FastD\Swoole\Listener\Server;

use FastD\Http\Request\SwooleServerRequest;
use FastD\Http\Response\Json;
use FastD\Http\Response\SseResponse;
use FastD\Swoole\Event\Server\RequestEvent;
use FastD\Swoole\Event\SwooleEvent;
use FastD\Swoole\Listener\SwooleEventListener;
use FastD\Swoole\Sse\SwooleEmitter;
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

                $clientInfo = $event->object->swoole->getClientInfo($request->fd);

                if (is_array($clientInfo) && ($clientInfo['server_port'] ?? null) == $this->port) {
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
        $this->sendHeaders($swooleResponse, $response);

        if ($response instanceof SseResponse) {
            // SSE：逐帧写出，由 SwooleEmitter 负责心跳、背压、断连取消与结束前的收尾
            SwooleEmitter::fromResponse($swooleResponse)->run($response);

            return;
        }

        // send contents
        $swooleResponse->end((string) $response->getBody());
    }

    /**
     * 发送状态行、响应头与 Cookie。
     */
    protected function sendHeaders(Response $swooleResponse, ResponseInterface $response): void
    {
        foreach ($response->getHeaders() as $name => $values) {
            if (0 === strcasecmp($name, 'Set-Cookie')) {
                // Swoole header() keeps only the last value for a given name,
                // so cookies must go through Response::cookie() instead.
                foreach ($values as $value) {
                    $swooleResponse->header('Set-Cookie', $value);
                }
                continue;
            }
            $swooleResponse->header($name, implode(', ', $values));
        }

        // FastD responses keep cookies as objects; map them to Swoole's
        // structured cookie API (name, value, absolute expiry, attributes).
        foreach ($this->extractCookies($response) as $cookie) {
            $swooleResponse->cookie(...$cookie);
        }

        $swooleResponse->status($response->getStatusCode());
    }

    /**
     * Map cookie objects carried by the response to Swoole cookie arguments.
     *
     * FastD\Http\Cookie treats `expire` as a relative number of seconds and an
     * empty value as "delete this cookie"; Swoole expects an absolute
     * timestamp, so both rules are translated here.
     *
     * @return list<array{0: string, 1: string, 2: int, 3: string, 4: string, 5: bool, 6: bool, 7: string}>
     */
    protected function extractCookies(ResponseInterface $response): array
    {
        if (!method_exists($response, 'getCookies')) {
            return [];
        }

        $cookies = [];

        foreach ($response->getCookies() as $cookie) {
            if (!is_object($cookie) || !method_exists($cookie, 'getName')) {
                continue;
            }

            $value = (string) $cookie->getValue();

            if ('' === $value) {
                $expires = time() - 31536001;
            } elseif ($cookie->getExpire() > 0) {
                $expires = time() + (int) $cookie->getExpire();
            } else {
                $expires = 0;
            }

            $cookies[] = [
                (string) $cookie->getName(),
                $value,
                $expires,
                (string) $cookie->getPath(),
                (string) $cookie->getDomain(),
                (bool) $cookie->isSecure(),
                (bool) $cookie->isHttpOnly(),
                (string) $cookie->getSameSite(),
            ];
        }

        return $cookies;
    }
}
