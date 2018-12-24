<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Handlers;


use swoole_http_request;
use swoole_http_response;
use swoole_server;
use swoole_websocket_frame;

/**
 * Interface WebSocketServerHandlerInterface
 * @package FastD\Swoole\Handlers
 */
interface WebSocketServerHandlerInterface
{
    /**
     * @param swoole_http_request $request
     * @param swoole_http_response $response
     * @return mixed
     */
    public function onHandShake(swoole_http_request $request, swoole_http_response $response);

    /**
     * @param swoole_websocket_server $server
     * @param swoole_http_request $request
     * @return mixed
     */
    public function onOpen(swoole_websocket_server $server, swoole_http_request $request);

    /**
     * @param swoole_server $server
     * @param swoole_websocket_frame $frame
     * @return mixed
     */
    public function onMessage(swoole_server $server, swoole_websocket_frame $frame);
}