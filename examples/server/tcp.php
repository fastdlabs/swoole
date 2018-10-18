<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

include __DIR__ . '/../../vendor/autoload.php';

class Server extends \FastD\Swoole\TCPServer
{
    /**
     * @param swoole_server $server
     * @param $fd
     * @param $from_id
     */
    public function onConnect(swoole_server $server, int $fd, int $from_id): void
    {
        // TODO: Implement onConnect() method.
    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param $fromId
     */
    public function onClose(swoole_server $server, int $fd, int $fromId): void
    {
        // TODO: Implement onClose() method.
    }

    /**
     * @param swoole_server $server
     * @param int $src_worker_id
     * @param string $message
     */
    public function onPipeMessage(swoole_server $server, int $src_worker_id, string $message): void
    {
        // TODO: Implement onPipeMessage() method.
    }

    /**
     * @param swoole_server $server
     * @param int $fd
     * @param int $reactor_id
     * @param string $data
     */
    public function onReceive(swoole_server $server, int $fd, int $reactor_id, string $data): void
    {
        $server->send($fd, 'hello world');
        $server->close($fd);
    }
}

Server::createServer('127.0.0.1:9876')->start();
