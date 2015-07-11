<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/11
 * Time: 上午10:12
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole;

class SwooleHandler implements SwooleHandlerInterface
{
    public function onStart(\swoole_server $server)
    {
        // TODO: Implement onStart() method.
    }

    public function onShutdown(\swoole_server $server)
    {
        // TODO: Implement onShutdown() method.
    }

    public function onWorkerStart(\swoole_server $server, $worker_id)
    {
        // TODO: Implement onWorkerStart() method.
    }

    public function onWorkerStop(\swoole_server $server, $worker_id)
    {
        // TODO: Implement onWorkerStop() method.
    }

    public function onTimer(\swoole_server $server, $interval)
    {
        // TODO: Implement onTimer() method.
    }

    public function onConnect(\swoole_server $server, $fd, $from_id)
    {
        // TODO: Implement onConnect() method.
    }

    public function onReceive(\swoole_server $server, $fd, $from_id, $data)
    {
        // TODO: Implement onReceive() method.
    }

    /**
     * swoole v1.7.18+
     */
    public function onPacket(\swoole_server $server, $data, $client_info)
    {
        // TODO: Implement onPacket() method.
    }

    public function onClose(\swoole_server $server, $fd, $from_id)
    {
        // TODO: Implement onClose() method.
    }

    public function onTask(\swoole_server $server, $task_id, $from_id, $data)
    {
        // TODO: Implement onTask() method.
    }

    public function onFinish(\swoole_server $server, $task_id, $data)
    {
        // TODO: Implement onFinish() method.
    }

    public function onPipeMessage(\swoole_server $server, $from_worker_id, $message)
    {
        // TODO: Implement onPipeMessage() method.
    }

    public function onWorkerError(\swoole_server $server, $worker_id, $worker_pid, $exit_mode)
    {
        // TODO: Implement onWorkerError() method.
    }

    public function onManagerStart(\swoole_server $server)
    {
        // TODO: Implement onManagerStart() method.
    }

    public function onManagerStop(\swoole_server $server)
    {
        // TODO: Implement onManagerStop() method.
    }
}