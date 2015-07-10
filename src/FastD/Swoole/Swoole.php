<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/9
 * Time: 下午6:23
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole;

class Swoole implements SwooleInterface
{
    /**
     * @var \swoole_server
     */
    protected $server;

    protected $context;

    protected $prepareBind = [
        'start' => 'onStart',
        'shutdown' => 'onShutdown',
        'workerStart' => 'onWorkerStart',
        'workerStop' => 'onWorkerStop',
        'timer' => 'onTimer',
        'connect' => 'onConnect',
        'receive' => 'onReceive',
        'packet' => 'onPacket',
        'close' => 'onClose',
        'task' => 'onTask',
        'finish' => 'onFinish',
        'pipeMessage' => 'onPipeMessage',
        'workerError' => 'onWorkerError',
        'managerStart' => 'onManagerStart',
        'managerStop' => 'onManagerStop',
    ];

    public function __construct(Context $context, $mode = SWOOLE_PROCESS, $sockType = SWOOLE_SOCK_TCP)
    {
        $this->server = new \swoole_server($context->getScheme(), $context->getPort(), $mode, $sockType);

        $this->context = $context;
    }

    public static function create($protocol, array $config = [])
    {
        return new static(new Context($protocol, $config));
    }

    public function run()
    {
        $this->server->set($this->context->all());

        foreach ($this->prepareBind as $name => $callback) {
            $this->server->on($name, [$this, $callback]);
        }

        $this->server->start();
    }

    public function daemonize()
    {
        $this->context->set('daemonize', true);

        return $this;
    }

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