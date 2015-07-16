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

namespace FastD\Swoole\Server;

use FastD\Swoole\SwooleInterface;

/**
 * Class SwooleHandler
 *
 * @package FastD\Swoole
 */
abstract class ServerHandler implements ServerHandlerInterface
{
    /**
     * @var array
     */
    protected $on;

    /**
     * @var Swoole
     */
    protected $swoole;

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
    ];

    /**
     * @param array $on
     */
    public function __construct(array $on = null)
    {
        if (null === $on) {
            $on = array_keys($this->prepareBind);
        }

        $this->setPrepareBind($on);
    }

    /**
     * @param array $on
     * @return $this
     */
    public function setPrepareBind(array $on)
    {
        foreach ($on as $name) {
            $this->on[$name] = $this->prepareBind[$name];
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getPrepareBind()
    {
        return $this->on;
    }

    /**
     * @param SwooleInterface $swooleInterface
     * @return $this
     */
    public function handle(SwooleInterface $swooleInterface)
    {
        $this->swoole = $swooleInterface;

        foreach ($this as $name => $callback) {
            $swooleInterface->on($name, [$this, $callback]);
        }

        return $this;
    }

    /**
     * @param $name
     * @return void
     */
    public function rename($name)
    {
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($name);
        } else if (function_exists('swoole_set_process_name')) {
            swoole_set_process_name($name);
        }
    }

    /**
     * @param \swoole_server $server
     * @return mixed
     */
    public function onStart(\swoole_server $server)
    {
        if (null !== ($sock = $this->swoole->getContext()->get('sock_file'))) {
            if (!is_dir($dir = dirname($sock))) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($sock, $server->master_pid . PHP_EOL);
        }

        $this->rename($this->swoole->getContext()->hasGet('process_name', 'swoole') . ' master');
    }

    /**
     * @param \swoole_server $server
     * @return mixed
     */
    public function onManagerStart(\swoole_server $server)
    {
        $this->rename($this->swoole->getContext()->hasGet('process_name', 'swoole') . ' manager');
    }

    /**
     * @param \swoole_server $server
     * @param                $worker_id
     * @return mixed
     */
    public function onWorkerStart(\swoole_server $server, $worker_id)
    {
        $this->rename($this->swoole->getContext()->hasGet('process_name', 'swoole') . ' worker');
    }

    /**
     * @param \swoole_server $server
     * @return mixed
     */
    abstract public function onShutdown(\swoole_server $server);

    /**
     * @param \swoole_server $server
     * @param                $worker_id
     * @return mixed
     */
    abstract public function onWorkerStop(\swoole_server $server, $worker_id);

    /**
     * @param \swoole_server $server
     * @param                $interval
     * @return mixed
     */
    abstract public function onTimer(\swoole_server $server, $interval);

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @return mixed
     */
    abstract public function onConnect(\swoole_server $server, $fd, $from_id);

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @param                $data
     * @return mixed
     */
    abstract public function onReceive(\swoole_server $server, $fd, $from_id, $data);

    /**
     * swoole v1.7.18+
     *
     * @param $server
     * @param $data
     * @param $client_info;
     */
    abstract public function onPacket(\swoole_server $server, $data, $client_info);

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @return mixed
     */
    abstract public function onClose(\swoole_server $server, $fd, $from_id);

    /**
     * @param \swoole_server $server
     * @param                $task_id
     * @param                $from_id
     * @param                $data
     * @return mixed
     */
    abstract public function onTask(\swoole_server $server, $task_id, $from_id, $data);

    /**
     * @param \swoole_server $server
     * @param                $task_id
     * @param                $data
     * @return mixed
     */
    abstract public function onFinish(\swoole_server $server, $task_id, $data);

    /**
     * @param \swoole_server $server
     * @param                $from_worker_id
     * @param                $message
     * @return mixed
     */
    abstract public function onPipeMessage(\swoole_server $server, $from_worker_id, $message);

    /**
     * @param \swoole_server $server
     * @param                $worker_id
     * @param                $worker_pid
     * @param                $exit_mode
     * @return mixed
     */
    abstract public function onWorkerError(\swoole_server $server, $worker_id, $worker_pid, $exit_mode);

    /**
     * @param \swoole_server $server
     * @return mixed
     */
    abstract public function onManagerStop(\swoole_server $server);
}