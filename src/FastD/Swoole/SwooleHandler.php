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

/**
 * Class SwooleHandler
 *
 * @package FastD\Swoole
 */
class SwooleHandler implements SwooleHandlerInterface
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
        'request'       => 'onRequest',
    ];

    /**
     * @param array $on
     */
    public function __construct(array $on = [])
    {
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
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->on);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->on);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->on);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->on[$this->key()]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->on);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function rename($name)
    {
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($name);
        } else if (function_exists('swoole_set_process_name')) {
            swoole_set_process_name($name);
        }

        return $name;
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
     * @param \swoole_server $server
     * @return mixed
     */
    public function onStart(\swoole_server $server)
    {
        if (null !== ($pid = $this->swoole->getContext()->get('pid'))) {
            if (!is_dir($dir = dirname($pid))) {
                mkdir($dir, 0755, true);
            }

            $serverInfo = [
                'pid' => $server->master_pid,
                'server' => serialize($server),
            ];

            file_put_contents($pid, json_encode($serverInfo, JSON_UNESCAPED_UNICODE) . PHP_EOL);
        }

        $this->rename($this->swoole->getContext()->hasGet('master_name', 'swoole') . ' master');
    }

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
    public function onWorkerStart(\swoole_server $server, $worker_id)
    {
        $this->rename($this->swoole->getContext()->hasGet('master_name', 'swoole') . ' worker');
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
    public function onManagerStart(\swoole_server $server)
    {
        $this->rename($this->swoole->getContext()->hasGet('master_name', 'swoole') . ' manager');
    }

    /**
     * @param \swoole_server $server
     * @return mixed
     */
    public function onManagerStop(\swoole_server $server)
    {
        // TODO: Implement onManagerStop() method.
    }

    /**
     * @param \swoole_http_request  $request
     * @param \swoole_http_response $response
     * @return void
     */
    public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
    {
        $response->end('hello world');
    }
}