<?php

declare(strict_types=1);

namespace FastD\Swoole\Server;

use FastD\Http\SwooleRequest;
use FastD\Swoole\Server\Callback\HTTPCallbackInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server as HTTPServer;
use Swoole\Server;

class HTTP extends Swoole implements HTTPCallbackInterface
{
    protected string $protocol = 'http';

    protected string $handler;

    public function createSwooleServer(): \Swoole\Server
    {
        return new HTTPServer($this->host, $this->port);
    }

    public function enableHTTP2(string $key, string $cert): HTTP
    {
        $this->config['open_http2_protocol'] = true;
        $this->config['ssl_cert_file'] = $cert;
        $this->config['ssl_key_file'] = $key;

        return $this;
    }

    /**
     * @param Request $swooleRequet
     * @param Response $swooleResponse
     */
    public function onRequest(Request $request, Response $response): void
    {
        $serverRequest = SwooleRequest::createServerRequestFromSwoole($request);

        $this->handleResponse($response, $this->handleRequest($serverRequest));
    }

    public function handleRequest(SwooleRequest $serverRequest, Response $response): \FastD\Http\Response
    {
        return new \FastD\Http\Response('helle swoole');
    }

    /**
     * @param \Swoole\Http\Response $swooleResponse
     * @param \FastD\Http\Response $response
     */
    protected function sendHeader(Response $swooleResponse, \FastD\Http\Response $response)
    {
        foreach ($response->getHeaders() as $key => $header) {
            $swooleResponse->header($key, $response->getHeaderLine($key));
        }

        foreach ($response->getCookies() as $key => $cookieParam) {
            $swooleResponse->cookie($key, $cookieParam);
        }
    }

    /**
     * @param \Swoole\Http\Response $swooleResponse
     * @param Response $response
     * @return void
     */
    protected function handleResponse(Response $swooleResponse, \FastD\Http\Response $response): void
    {
        $this->sendHeader($swooleResponse, $response);
        $swooleResponse->status($response->getStatusCode());
        $swooleResponse->end((string) $response->getBody());
    }

    /**
     * @param \Swoole\Server $server
     * @return void
     */
    public function onStart(Server $server): void
    {
    }

    /**
     * @param Server $server
     * @return void
     */
    public function onShutdown(Server $server): void
    {
    }

    /**
     * @param Server $server
     * @return void
     */
    public function onManagerStart(Server $server): void
    {
    }

    /**
     * @param Server $server
     * @return void
     */
    public function onManagerStop(Server $server): void
    {
    }

    /**
     * @param Server $server
     * @param int $id
     * @return void
     */
    public function onWorkerStart(Server $server, int $id): void
    {
    }

    /**
     * @param Server $server
     * @param int $id
     */
    public function onWorkerStop(Server $server, int $id): void
    {
    }

    /**
     * @param Server $server
     * @param int $id
     * @param int $worker_pid
     * @param int $exit_code
     * @param int $signal
     */
    public function onWorkerError(Server $server, int $id, int $worker_pid, int $exit_code, int $signal): void
    {
    }

    /**
     * @param Server $server
     * @param int $id
     */
    public function onWorkerExit(Server $server, int $id): void
    {
    }

    /**
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose(Server $server, int $fd, int $reactorId): void
    {
    }

    public function onConnect(\Swoole\Server $server, int $fd, int $reactorId): void
    {

    }

    public function onReceive(\Swoole\Server $server, int $fd, int $reactorId, string $data): bool
    {

    }
}
