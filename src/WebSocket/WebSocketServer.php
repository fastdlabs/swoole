<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\WebSocket;

use FastD\Swoole\Server;
use swoole_server;
use swoole_websocket_server;
use swoole_http_request;
use swoole_http_response;
use swoole_websocket_frame;

abstract class WebSocketServer extends Server
{
    /**
     * @param swoole_server $server
     * @param $fd
     * @param $data
     * @param $from_id
     * @return mixed
     */
    public function doWork(swoole_server $server, $fd, $data, $from_id){}

    /**
     * @param swoole_server $server
     * @param $data
     * @param $client_info
     * @return mixed
     */
    public function doPacket(swoole_server $server, $data, $client_info){}

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
     * @param swoole_http_request $request
     * @param swoole_http_response $response
     * @return mixed
     */
    public function onHandShake(swoole_http_request $request, swoole_http_response $response)
    {
        return $this->doHandShake($request, $response);
    }

    /**
     * @param swoole_http_request $request
     * @param swoole_http_response $response
     * @return mixed
     */
    abstract public function doHandShake(swoole_http_request $request, swoole_http_response $response);

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
     * @return swoole_websocket_server
     */
    public function initSwoole()
    {
        return new swoole_websocket_server($this->getHost(), $this->getPort());
    }
}