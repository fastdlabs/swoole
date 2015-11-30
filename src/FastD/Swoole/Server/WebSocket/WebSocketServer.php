<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/9/13
 * Time: 下午4:20
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\WebSocket;

use FastD\Swoole\Context;
use FastD\Swoole\Swoole;

class WebSocketServer extends Swoole
{
    /**
     * @param Context $context
     * @param         $mode
     * @param         $sockType
     */
    public function __construct(Context $context, $mode = SWOOLE_PROCESS, $sockType = SWOOLE_SOCK_TCP)
    {
        $this->initPid($context);

        $this->server = new \swoole_websocket_server($context->getHost(), $context->getPort());

        $this->context = $context;
    }
}