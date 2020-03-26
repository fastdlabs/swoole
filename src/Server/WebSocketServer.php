<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;


use FastD\Swoole\Handlers\WebSocketServerHandlerInterface;
use Swoole\Http\Request;
use Swoole\WebSocket\Server;
use Swoole\WebSocket\Frame;

/**
 * Class WebSocketServer
 * @package FastD\Swoole
 */
abstract class WebSocketServer extends ServerAbstract implements WebSocketServerHandlerInterface
{
    protected $protocol = 'ws';

    /**
     * @return swoole_websocket_server
     */
    public function initSwoole(): \Swoole\Server
    {
        return new swoole_websocket_server($this->host, $this->port);
    }

    /**
     * @param Server $server
     * @param Request $request
     * @return mixed
     */
    abstract public function onOpen(Server $server, Request $request);

    /**
     * @param Server $server
     * @param Frame $frame
     * @return mixed
     */
    abstract public function onMessage(Server $server, Frame $frame);

    /**
     * @param Server $server
     * @param Frame $frame
     * @return mixed
     */
    abstract public function doMessage(Server $server, Frame $frame);
}