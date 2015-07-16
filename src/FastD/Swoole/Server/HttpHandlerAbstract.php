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

namespace FastD\Swoole\Server;

abstract class HttpHandlerAbstract extends ServerHandlerAbstract
{
    /**
     * @param \swoole_http_request  $request
     * @param \swoole_http_response $response
     * @return mixed
     */
    abstract public function onRequest(\swoole_http_request $request, \swoole_http_response $response);

    /**
     * @param \swoole_server $server
     * @return mixed
     */
    public function onShutdown(\swoole_server $server)
    {
        // TODO: Implement onShutdown() method.
    }

    /**
     * @param \swoole_server $server
     * @param                $worker_id
     * @return mixed
     */
    public function onWorkerStop(\swoole_server $server, $worker_id)
    {
        // TODO: Implement onWorkerStop() method.
    }

    /**
     * @param \swoole_server $server
     * @param                $interval
     * @return mixed
     */
    public function onTimer(\swoole_server $server, $interval)
    {
        // TODO: Implement onTimer() method.
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
     * swoole v1.7.18+
     *
     * @param $server
     * @param $data
     * @param $client_info ;
     */
    public function onPacket(\swoole_server $server, $data, $client_info)
    {
        // TODO: Implement onPacket() method.
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

    /**
     * @param \swoole_server $server
     * @param                $task_id
     * @param                $from_id
     * @param                $data
     * @return mixed
     */
    public function onTask(\swoole_server $server, $task_id, $from_id, $data)
    {
        // TODO: Implement onTask() method.
    }

    /**
     * @param \swoole_server $server
     * @param                $task_id
     * @param                $data
     * @return mixed
     */
    public function onFinish(\swoole_server $server, $task_id, $data)
    {
        // TODO: Implement onFinish() method.
    }

    /**
     * @param \swoole_server $server
     * @param                $from_worker_id
     * @param                $message
     * @return mixed
     */
    public function onPipeMessage(\swoole_server $server, $from_worker_id, $message)
    {
        // TODO: Implement onPipeMessage() method.
    }

    /**
     * @param \swoole_server $server
     * @param                $worker_id
     * @param                $worker_pid
     * @param                $exit_mode
     * @return mixed
     */
    public function onWorkerError(\swoole_server $server, $worker_id, $worker_pid, $exit_mode)
    {
        // TODO: Implement onWorkerError() method.
    }

    /**
     * @param \swoole_server $server
     * @return mixed
     */
    public function onManagerStop(\swoole_server $server)
    {
        // TODO: Implement onManagerStop() method.
    }
}