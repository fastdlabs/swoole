<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/12
 * Time: 下午6:20
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Rpc;

use FastD\Swoole\TcpServer\TcpHandler;

class RpcHandler extends TcpHandler
{
    /**
     * @var RpcServer
     */
    protected $swoole;

    public function onReceive(\swoole_server $server, $fd, $from_id, $data)
    {
        $protocol = $this->swoole->getProtocol();
        $data = $this->swoole->decode($protocol, $data);
        $callback = $this->swoole->getCallback($data[RpcInterface::RPC_ACTION]);

        echo 'receive' . PHP_EOL;
        $server->send($fd, call_user_func_array($callback, $data[RpcInterface::RPC_ARGS]));
        $server->close($fd);
    }

    public function onConnect(\swoole_server $server, $fd, $from_id)
    {
        echo 'connection' . PHP_EOL;
    }

    public function onClose(\swoole_server $server, $fd, $from_id)
    {
        echo 'close' . PHP_EOL;
    }
}