<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/10
 * Time: 上午11:55
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */
namespace FastD\Swoole;

interface SwooleInterface
{
    public function onStart(\swoole_server $server);

    public function onShutdown(\swoole_server $server);

    public function onWorkerStart(\swoole_server $server, $worker_id);

    public function onWorkerStop(\swoole_server $server, $worker_id);

    public function onTimer(\swoole_server $server, $interval);

    public function onConnect(\swoole_server $server, $fd, $from_id);

    public function onReceive(\swoole_server $server, $fd, $from_id, $data);

    /**
     * swoole v1.7.18+
     */
    public function onPacket(\swoole_server $server, $data, $client_info);

    public function onClose(\swoole_server $server, $fd, $from_id);

    public function onTask(\swoole_server $server, $task_id, $from_id, $data);

    public function onFinish(\swoole_server $server, $task_id, $data);

    public function onPipeMessage(\swoole_server $server, $from_worker_id, $message);

    public function onWorkerError(\swoole_server $server, $worker_id, $worker_pid, $exit_mode);

    public function onManagerStart(\swoole_server $server);

    public function onManagerStop(\swoole_server $server);
}