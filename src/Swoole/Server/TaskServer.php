<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;

/**
 * Class TaskServer
 *
 * @package FastD\Swoole\Server
 */
abstract class TaskServer extends Server
{
    /**
     * @param \swoole_server $server
     * @param int $task_id
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    public function onTask(\swoole_server $server, int $task_id, int $from_id, string $data)
    {
        return $this->doTask($server, $task_id, $from_id, $data);
    }

    /**
     * @param \swoole_server $server
     * @param int $task_id
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    abstract public function doTask(\swoole_server $server, int $task_id, int $from_id, string $data);

    /**
     * @param \swoole_server $server
     * @param int $task_id
     * @param string $data
     * @return mixed
     */
    public function onFinish(\swoole_server $server, int $task_id, string $data)
    {
        $finish = $this->doFinish($server, $task_id, $data);

        $this->report($server, $server->worker_pid, $task_id, [
            'name' => 'task ' . $task_id
        ]);

        return $finish;
    }

    /**
     * @param \swoole_server $server
     * @param int $task_id
     * @param string $data
     * @return mixed
     */
    abstract public function doFinish(\swoole_server $server, int $task_id, string $data);
}