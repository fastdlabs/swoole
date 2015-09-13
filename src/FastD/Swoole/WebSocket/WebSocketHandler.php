<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/9/13
 * Time: 下午4:22
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\WebSocket;

use FastD\Swoole\Server\ServerHandlerAbstract;

class WebSocketHandler extends ServerHandlerAbstract
{
    protected $prepareBind = [
        'start'         => 'onStart',
        'shutdown'      => 'onShutdown',
        'workerStart'   => 'onWorkerStart',
        'workerStop'    => 'onWorkerStop',
        'timer'         => 'onTimer',
        'connect'       => 'onConnect',
        'receive'       => 'onReceive',
        'packet'        => 'onPacket',
        'close'         => 'onClose',
        'task'          => 'onTask',
        'finish'        => 'onFinish',
        'pipeMessage'   => 'onPipeMessage',
        'workerError'   => 'onWorkerError',
        'managerStart'  => 'onManagerStart',
        'managerStop'   => 'onManagerStop',
        'open'          => 'onOpen',
        'message'       => 'onMessage',
    ];

    public function onOpen($ws, $request)
    {
        var_dump($request->fd, $request->get, $request->server);
        $ws->push($request->fd, "hello, welcome\n");
    }

    public function onMessage($ws, $frame)
    {
        echo "Message: {$frame->data}\n";
        $ws->push($frame->fd, "server: {$frame->data}");
    }

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @return mixed
     */
    public function onConnect(\swoole_server $server, $fd, $from_id)
    {
        // TODO: Implement onConnect() method.
    }

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @param                $data
     * @return mixed
     */
    public function onReceive(\swoole_server $server, $fd, $from_id, $data)
    {
        // TODO: Implement onReceive() method.
    }

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @return mixed
     */
    public function onClose(\swoole_server $server, $fd, $from_id)
    {
        // TODO: Implement onClose() method.
    }
}