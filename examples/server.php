<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
include __DIR__ . '/../vendor/autoload.php';

use FastD\Http\Request;
use Swoole\Server;

class BaseServer extends \FastD\Swoole\HTTPServer
{
    /**
     * @param Request $request
     * @return \FastD\Http\Response
     */
    public function handleRequest(Request $request): \FastD\Http\Response
    {
        return new \FastD\Http\Response('hello world');
    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param $from_id
     */
    public function onConnect(Server $server, int $fd, int $from_id): void
    {
        // TODO: Implement onConnect() method.
    }

    /**
     * @param Server $server
     * @param $fd
     * @param $fromId
     */
    public function onClose(Server $server, int $fd, int $fromId): void
    {
        // TODO: Implement onClose() method.
    }

    /**
     * @param Server $server
     * @param int $src_worker_id
     * @param string $message
     */
    public function onPipeMessage(Server $server, int $src_worker_id, string $message): void
    {
        // TODO: Implement onPipeMessage() method.
    }
}

BaseServer::createServer()->enableHTTP2()->start();
