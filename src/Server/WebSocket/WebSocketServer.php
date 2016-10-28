<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server\WebSocket;

use FastD\Swoole\Server;
use swoole_server;
use swoole_websocket_server;
use swoole_http_request;
use swoole_http_response;
use swoole_websocket_frame;

abstract class WebSocketServer extends Server
{
    /**
     * @param swoole_websocket_server $server
     * @param swoole_http_request $request
     * @return mixed
     */
    public function onOpen(swoole_websocket_server $server, swoole_http_request $request)
    {
        return $this->doOpen($server, $request);
    }

    /**
     * @param swoole_websocket_server $server
     * @param swoole_http_request $request
     * @return mixed
     */
    abstract public function doOpen(swoole_websocket_server $server, swoole_http_request $request);

    /**
     * @param swoole_server $server
     * @param swoole_websocket_frame $frame
     * @return mixed
     */
    public function onMessage(swoole_server $server, swoole_websocket_frame $frame)
    {
        return $this->doMessage($server, $frame);
    }

    /**
     * @param swoole_server $server
     * @param swoole_websocket_frame $frame
     * @return mixed
     */
    abstract public function doMessage(swoole_server $server, swoole_websocket_frame $frame);

    /**
     * @return swoole_server
     */
    public function initSwoole()
    {
        return new swoole_websocket_server($this->getHost(), $this->getPort());
    }
}