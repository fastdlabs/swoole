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
    protected $on;

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

    public function __construct(array $on = [])
    {
        $this->setPrepareBind($on);
    }

    public function setPrepareBind(array $on)
    {
        foreach ($on as $name) {
            $this->on[$name] = $this->prepareBind[$name];
        }

        return $this;
    }

    public function getPrepareBind()
    {
        return $this->on;
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
     *
     * @param \swoole_server $server
     * @param                $data
     * @param                $client_info
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

    public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
    {
        $response->end('hello world');
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

    public function handle(SwooleInterface $swooleInterface)
    {
        foreach ($this as $name => $callback) {
            $swooleInterface->on($name, [$this, $callback]);
        }

        return $this;
    }
}