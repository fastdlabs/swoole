<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

use FastD\Http\HttpException;
use FastD\Http\Response;
use FastD\Http\ServerRequest;
use FastD\Http\SwooleServerRequest;
use FastD\Swoole\Handlers\HTTPHandlerInterface;

include __DIR__ . '/../../vendor/autoload.php';

$http = new class extends \FastD\Swoole\Server\HTTPServer {
    /**
     * @param \FastD\Http\Request $request
     * @return \FastD\Http\Response
     */
    public function handleRequest(ServerRequest $request): \FastD\Http\Response
    {
        return new \FastD\Http\Response("hello");
    }
};

$handler = new class implements HTTPHandlerInterface
{
    /**
     * @param \Swoole\Http\Request $swooleRequet
     * @param \Swoole\Http\Response $swooleResponse
     */
    public function onRequest(\Swoole\Http\Request $swooleRequet, \Swoole\Http\Response $swooleResponse): void
    {
        output(sprintf("request: <info>%s</info>", $swooleRequet->server['path_info']));
        $swooleResponse->end('hello');
    }

    /**
     * @param \Swoole\Server $server
     * @return bool
     */
    public function onStart(\Swoole\Server $server): bool
    {
        return  true;
    }

    /**
     * @param \Swoole\Server $server
     * @return bool
     */
    public function onShutdown(\Swoole\Server $server): bool
    {
        return  true;
    }

    /**
     * @param \Swoole\Server $server
     * @return bool
     */
    public function onManagerStart(\Swoole\Server $server): bool
    {
        return  true;
    }

    /**
     * @param \Swoole\Server $server
     * @return bool
     */
    public function onManagerStop(\Swoole\Server $server): bool
    {
        return  true;
    }

    /**
     * @param \Swoole\Server $server
     * @param int $id
     * @return bool
     */
    public function onWorkerStart(\Swoole\Server $server, int $id): bool
    {
        return  true;
    }

    /**
     * @param \Swoole\Server $server
     * @param int $id
     * @return bool
     */
    public function onWorkerStop(\Swoole\Server $server, int $id): bool
    {
        return  true;
    }

    /**
     * @param \Swoole\Server $server
     * @param int $worker_id
     * @param int $worker_pid
     * @param int $exit_code
     * @param int $signal
     * @return bool
     */
    public function onWorkerError(
        \Swoole\Server $server,
        int $worker_id,
        int $worker_pid,
        int $exit_code,
        int $signal
    ): bool {
        return  true;
    }

    /**
     * @param \Swoole\Server $server
     * @param int $id
     * @return bool
     */
    public function onWorkerExit(\Swoole\Server $server, int $id): bool
    {
        return  true;
    }

    /**
     * @param \Swoole\Server $server
     * @param int $fd
     * @param int $id
     * @return bool
     */
    public function onClose(\Swoole\Server $server, int $fd, int $id): bool
    {
        return  true;
    }

    /**
     * @param \FastD\Swoole\Handlers\Swoole $server
     * @param int $src_worker_id
     * @param $message
     * @return bool
     */
    public function onPipeMessage(\Swoole\Server $server, int $src_worker_id, $message): bool
    {
        return  true;
    }
};

$server = new $http();
$server->handler(new $handler());

$server->start();
