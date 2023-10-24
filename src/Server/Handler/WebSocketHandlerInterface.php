<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Server\Handler;


use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Interface WebSocketServerHandlerInterface
 * @package FastD\Swoole\Handlers
 */
interface WebSocketHandlerInterface
{

    /**
     * @param Server $server
     * @param Request $request
     */
    public function onOpen(Server $server, Request $request): void;

    /**
     * @param Server $server
     * @param Frame $frame
     */
    public function onMessage(Server $server, Frame $frame): void ;
}
