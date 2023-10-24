<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;


use FastD\Swoole\Server\Handler\WebSocketHandlerInterface;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Class WebSocketServer
 * @package FastD\Swoole
 */
class WebSocket extends AbstractServer implements WebSocketHandlerInterface
{
    protected string $protocol = 'ws';

    /**
     * @param Server $server
     * @param Request $request
     * @return mixed
     */
    public function onOpen(Server $server, Request $request)
    {
        output(sprintf('fd [%s]'));
        $server->push($request->fd, "hello, welcome\n");
    }

    /**
     * @param Server $server
     * @param Frame $frame
     * @return mixed
     */
    public function onMessage(Server $server, Frame $frame)
    {
        output(sprintf("Message: [{$frame->data}]"));
        $server->push($frame->fd, "server: {$frame->data}");
    }
}
