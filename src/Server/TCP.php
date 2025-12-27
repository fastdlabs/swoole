<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;

use FastD\Swoole\Server\Callback\TCPCallbackInterface;
use Swoole\Server;

/**
 * Class TCPServer
 * @package FastD\Swoole
 */
class TCP extends Swoole implements TCPCallbackInterface
{
    public function createSwooleExtServer(): \Swoole\Server
    {

    }

    public function onReceive(Server $server, int $fd, int $reactorId, string $data): bool
    {
        $server->send($fd, $data);
    }

    public function onStart(Server $server): void
    {
    }

    public function onShutdown(Server $server): void
    {
    }

    public function onManagerStart(Server $server): void
    {
    }

    public function onManagerStop(Server $server): void
    {
    }

    public function onWorkerStart(Server $server, int $id): void
    {
    }

    public function onWorkerStop(Server $server, int $id): void
    {
    }

    public function onWorkerError(Server $server, int $id, int $worker_pid, int $exit_code, int $signal): void
    {
    }

    public function onWorkerExit(Server $server, int $id): void
    {
    }

    public function onClose(Server $server, int $fd, int $reactorId): void
    {
    }

    public function onConnect(Server $server, int $fd, int $reactorId): void
    {
    }
}
