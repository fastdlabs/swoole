<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Handlers;


use FastD\Http\Response;
use FastD\Http\SwooleServerRequest;
use Throwable;

class HTTPHandler extends HandlerAbstract
{
    /**
     * @param \Swoole\Http\Request $swooleRequet
     * @param \Swoole\Http\Response $swooleResponse
     */
    public function onRequest(\Swoole\Http\Request $swooleRequet, \Swoole\Http\Response $swooleResponse): void
    {
        output(sprintf("request: <info>%s</info>", $swooleRequet->server['request_uri']));
        try {
            $swooleRequestServer = SwooleServerRequest::createServerRequestFromSwoole($swooleRequet);
            $response = $this->doRequest($swooleRequestServer);
            $this->sendHeader($swooleResponse, $response);
            $swooleResponse->status($response->getStatusCode());
            $swooleResponse->end((string) $response->getBody());
            unset($response);
        } catch (HttpException $e) {
            $swooleResponse->status($e->getStatusCode());
            $swooleResponse->end($e->getMessage());
        } catch (Throwable $e) {
            $swooleResponse->status(Response::HTTP_INTERNAL_SERVER_ERROR);
            $swooleResponse->end(Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR]);
        }
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function onStart(Server $server): bool
    {
        // TODO: Implement onStart() method.
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function onShutdown(Server $server): bool
    {
        // TODO: Implement onShutdown() method.
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function onManagerStart(Server $server): bool
    {
        // TODO: Implement onManagerStart() method.
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function onManagerStop(Server $server): bool
    {
        // TODO: Implement onManagerStop() method.
    }

    /**
     * @param Server $server
     * @param int $id
     * @return bool
     */
    public function onWorkerStart(Server $server, int $id): bool
    {
        // TODO: Implement onWorkerStart() method.
    }

    /**
     * @param Server $server
     * @param int $id
     * @return bool
     */
    public function onWorkerStop(Server $server, int $id): bool
    {
        // TODO: Implement onWorkerStop() method.
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
        // TODO: Implement onWorkerError() method.
    }

    /**
     * @param Server $server
     * @param int $id
     * @return bool
     */
    public function onWorkerExit(Server $server, int $id): bool
    {
        // TODO: Implement onWorkerExit() method.
    }

    /**
     * @param Server $server
     * @param int $fd
     * @param int $id
     * @return bool
     */
    public function onClose(Server $server, int $fd, int $id): bool
    {
        // TODO: Implement onClose() method.
    }

    /**
     * @param Swoole\Server $server
     * @param int $src_worker_id
     * @param $message
     * @return bool
     */
    public function onPipeMessage(Server $server, int $src_worker_id, $message): bool
    {
        // TODO: Implement onPipeMessage() method.
    }
}
