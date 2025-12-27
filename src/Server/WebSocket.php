<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;


use FastD\Swoole\Server\Callback\WebSocketCallbackInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Class WebSocketServer
 * @package FastD\Swoole
 */
class WebSocket extends Server implements WebSocketCallbackInterface
{
    protected string $protocol = 'ws';

    /**
     * @param Swoole $server
     * @param Request $request
     * @return mixed
     */
    public function onOpen(Server $server, Request $request)
    {
        $server->push($request->fd, "hello, welcome\n");
    }

    /**
     * @param Swoole $server
     * @param Frame $frame
     * @return mixed
     */
    public function onMessage(Server $server, Frame $frame)
    {
        $server->push($frame->fd, "server: {$frame->data}");
    }

    public function onStart(Server $server): void
    {
        // TODO: Implement onStart() method.
    }

    public function onShutdown(Server $server): void
    {
        // TODO: Implement onShutdown() method.
    }

    public function onManagerStart(Server $server): void
    {
        // TODO: Implement onManagerStart() method.
    }

    public function onManagerStop(Server $server): void
    {
        // TODO: Implement onManagerStop() method.
    }

    public function onWorkerStart(Server $server, int $id): void
    {
        // TODO: Implement onWorkerStart() method.
    }

    public function onWorkerStop(Server $server, int $id): void
    {
        // TODO: Implement onWorkerStop() method.
    }

    public function onWorkerError(Server $server, int $id, int $workerPid, int $exitCode, int $signal): void
    {
        // TODO: Implement onWorkerError() method.
    }

    public function onWorkerExit(Server $server, int $id): void
    {
        // TODO: Implement onWorkerExit() method.
    }

    public function onRequest(Request $request, Response $response): void
    {
        // TODO: Implement onRequest() method.
    }

    public function onClose(Server $server, int $fd, int $reactorId): void
    {
        // TODO: Implement onClose() method.
    }

    public function onConnect(Server $server, int $fd, int $reactorId): void
    {
        // TODO: Implement onConnect() method.
    }

    public function onReceive(Server $server, int $fd, int $reactorId, string $data): bool
    {
        // TODO: Implement onReceive() method.
    }

    public function onHandShake(Request $request, Response $response): void
    {
        // TODO: Implement onHandShake() method.
    }

    public function onDisconnect(Server $server, int $fd): void
    {
        // TODO: Implement onDisconnect() method.
    }
}
