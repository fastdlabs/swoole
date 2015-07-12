<?php

/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/12
 * Time: 下午4:14
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Server;

use FastD\Swoole\SwooleHandlerInterface as SwooleHandler;

interface ServerHandlerInterface extends SwooleHandler, \Iterator
{
    /**
     * @param \swoole_server $server
     * @return mixed
     */
    public function onStart(\swoole_server $server);

    /**
     * @param \swoole_server $server
     * @return mixed
     */
    public function onShutdown(\swoole_server $server);

    /**
     * @param \swoole_server $server
     * @param                $worker_id
     * @return mixed
     */
    public function onWorkerStart(\swoole_server $server, $worker_id);

    /**
     * @param \swoole_server $server
     * @param                $worker_id
     * @return mixed
     */
    public function onWorkerStop(\swoole_server $server, $worker_id);

    /**
     * @param \swoole_server $server
     * @param                $interval
     * @return mixed
     */
    public function onTimer(\swoole_server $server, $interval);

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @return mixed
     */
    public function onConnect(\swoole_server $server, $fd, $from_id);

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @param                $data
     * @return mixed
     */
    public function onReceive(\swoole_server $server, $fd, $from_id, $data);

    /**
     * swoole v1.7.18+
     */
    public function onPacket(\swoole_server $server, $data, $client_info);

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @return mixed
     */
    public function onClose(\swoole_server $server, $fd, $from_id);

    /**
     * @param \swoole_server $server
     * @param                $task_id
     * @param                $from_id
     * @param                $data
     * @return mixed
     */
    public function onTask(\swoole_server $server, $task_id, $from_id, $data);

    /**
     * @param \swoole_server $server
     * @param                $task_id
     * @param                $data
     * @return mixed
     */
    public function onFinish(\swoole_server $server, $task_id, $data);

    /**
     * @param \swoole_server $server
     * @param                $from_worker_id
     * @param                $message
     * @return mixed
     */
    public function onPipeMessage(\swoole_server $server, $from_worker_id, $message);

    /**
     * @param \swoole_server $server
     * @param                $worker_id
     * @param                $worker_pid
     * @param                $exit_mode
     * @return mixed
     */
    public function onWorkerError(\swoole_server $server, $worker_id, $worker_pid, $exit_mode);

    /**
     * @param \swoole_server $server
     * @return mixed
     */
    public function onManagerStart(\swoole_server $server);

    /**
     * @param \swoole_server $server
     * @return mixed
     */
    public function onManagerStop(\swoole_server $server);

    /**
     * @param \swoole_http_request  $request
     * @param \swoole_http_response $response
     * @return mixed
     */
    public function onRequest(\swoole_http_request $request, \swoole_http_response $response);
}