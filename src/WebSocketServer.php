<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;


use FastD\Swoole\Handlers\WebSocketServerHandlerInterface;
use swoole_websocket_server;
use swoole_server;
use swoole_http_request;
use swoole_websocket_frame;

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
    public function initSwoole(): swoole_server
    {
        return new swoole_websocket_server($this->host, $this->port);
    }

    /**
     * @param swoole_websocket_server $server
     * @param swoole_http_request $request
     * @return mixed
     */
    abstract public function onOpen(swoole_websocket_server $server, swoole_http_request $request);

    /**
     * @param swoole_server $server
     * @param swoole_websocket_frame $frame
     * @return mixed
     */
    abstract public function onMessage(swoole_server $server, swoole_websocket_frame $frame);

    /**
     * @param swoole_server $server
     * @param swoole_websocket_frame $frame
     * @return mixed
     */
    abstract public function doMessage(swoole_server $server, swoole_websocket_frame $frame);
}