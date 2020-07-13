<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Handlers;


use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Interface WebSocketServerHandlerInterface
 * @package FastD\Swoole\Handlers
 */
interface WebSocketHandlerInterface
{
    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function onHandShake(Request $request, Response $response);

    /**
     * @param Server $server
     * @param Request $request
     * @return mixed
     */
    public function onOpen(Server $server, Request $request);

    /**
     * @param Server $server
     * @param Frame $frame
     * @return mixed
     */
    public function onMessage(Server $server, Frame $frame);
}
