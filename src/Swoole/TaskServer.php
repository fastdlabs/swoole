<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;

/**
 * Class TaskServer
 *
 * @package FastD\Swoole\Server
 */
abstract class TaskServer extends Server
{
    /**
     * @param $data
     * @return string
     */
    protected function encodeTaskData($data)
    {
        return serialize($data);
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function decodeTaskData($data)
    {
        return unserialize($data);
    }

    /**
     * 服务器同时监听TCP/UDP端口时，收到TCP协议的数据会回调onReceive，收到UDP数据包回调onPacket
     *
     * @param \swoole_server $server
     * @param string $data
     * @param array $client_info
     */
    public function doPacket(\swoole_server $server, string $data, array $client_info)
    {
        $server->task($this->encodeTaskData([
            'cmd' => 'packet',
            'content' => [
                'data' => $data,
                'client_info' => $client_info,
            ]
        ]));
    }

    /**
     * @param \swoole_server $server
     * @param int $fd
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    public function doWork(\swoole_server $server, int $fd, int $from_id, string $data)
    {
        $server->task($this->encodeTaskData([
            'cmd' => 'receive',
            'content' => [
                'data' => $data,
                'fd' => $fd,
                'from_id' => $from_id
            ]
        ]));
    }

    /**
     * @param \swoole_server $server
     * @param int $task_id
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    public function onTask(\swoole_server $server, int $task_id, int $from_id, string $data)
    {
        $data = $this->decodeTaskData($data);

        $this->doTask($server, $data['content']['fd'] ?? null, $data['content']['data'], $task_id, $from_id);
        
        return $task_id;
    }

    /**
     * @param \swoole_server $server
     * @param int $fd
     * @param int $task_id
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    abstract public function doTask(\swoole_server $server, int $fd, string $data, int $task_id, int $from_id);

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
            'task_id' => $task_id
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