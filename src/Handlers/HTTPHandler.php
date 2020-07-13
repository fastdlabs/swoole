<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Handlers;


use FastD\Http\ServerRequest;
use FastD\Http\SwooleServerRequest;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server;

abstract class HTTPHandler extends HandlerAbstract implements HTTPHandlerInterface
{
    /**
     * @param ServerRequestInterface $serverRequest
     * @return \FastD\Http\Response
     */
    abstract public function handleRequest(ServerRequest $serverRequest): \FastD\Http\Response;

    /**
     * @param Request $swooleRequet
     * @param Response $swooleResponse
     */
    public function onRequest(Request $swooleRequet, Response $swooleResponse): void
    {
        $serverRequest = SwooleServerRequest::createServerRequestFromSwoole($swooleRequet);

        $response = $this->handleRequest($serverRequest);

        $this->handleResponse($swooleResponse, $response);
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

        foreach ($response->getCookieParams() as $key => $cookieParam) {
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
     * @param Server $server
     * @return bool
     */
    public function onStart(Server $server): bool
    {
        return true;
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function onShutdown(Server $server): bool
    {
        return true;
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function onManagerStart(Server $server): bool
    {
        return true;
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function onManagerStop(Server $server): bool
    {
        return true;
    }

    /**
     * @param Server $server
     * @param int $id
     * @return bool
     */
    public function onWorkerStart(Server $server, int $id): bool
    {
        return true;
    }

    /**
     * @param Server $server
     * @param int $id
     * @return bool
     */
    public function onWorkerStop(Server $server, int $id): bool
    {
        return true;
    }

    /**
     * @param Server $server
     * @param int $worker_id
     * @param int $worker_pid
     * @param int $exit_code
     * @param int $signal
     * @return bool
     */
    public function onWorkerError(Server $server, int $worker_id, int $worker_pid, int $exit_code, int $signal): bool
    {
        return true;
    }

    /**
     * @param Server $server
     * @param int $id
     * @return bool
     */
    public function onWorkerExit(Server $server, int $id): bool
    {
        return true;
    }

    /**
     * @param Server $server
     * @param int $fd
     * @param int $id
     * @return bool
     */
    public function onClose(Server $server, int $fd, int $id): bool
    {
        return true;
    }
}
