<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/16
 * Time: 下午7:15
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\HttpServer;

use FastD\Swoole\Server\ServerHandlerAbstract;

abstract class HttpHandlerAbstract extends ServerHandlerAbstract
{
    /**
     * @var array
     */
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
        'request'       => 'onRequest',
    ];

    /**
     * @param \swoole_http_request  $request
     * @param \swoole_http_response $response
     * @return mixed
     */
    abstract public function onRequest(\swoole_http_request $request, \swoole_http_response $response);

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