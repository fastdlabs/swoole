<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

include __DIR__ . '/../../vendor/autoload.php';

use FastD\Http\Response;

class Server extends \FastD\Swoole\HTTPServer
{
    /**
     * @param \FastD\Http\Request $request
     * @return \FastD\Http\Response
     */
    public function handleRequest(\FastD\Http\Request $request): \FastD\Http\Response
    {
        return new Response('hello world');
    }

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
}

Server::createServer('127.0.0.1:9876')
    ->enableHTTP2()
    ->start();