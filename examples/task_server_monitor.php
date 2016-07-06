<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../vendor/autoload.php';

use FastD\Swoole\Server\TaskServer;

class DemoServer extends TaskServer
{
    /**
     * @param \swoole_server $server
     * @param int $task_id
     * @param string $data
     * @return mixed
     */
    public function doFinish(\swoole_server $server, int $task_id, string $data)
    {
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
    public function doTask(\swoole_server $server, int $fd, string $data, int $task_id, int $from_id)
    {
        echo $data;
        echo $fd;
        $server->send($fd, 'hello world');
        $server->close($fd);
    }
}

DemoServer::run([
    'task_worker_num' => 4,
    'monitors' => [
        [
            'host' => '127.0.0.1',
            'port' => '9883',
            'sock' => SWOOLE_SOCK_TCP
        ]
    ]
]);

/**
 *
 * $server = new DemoServer();
 * $server->monitoring([
 * [
 *      'host' => '127.0.0.1',
 *      'port' => '9883',
 *      'sock' => SWOOLE_SOCK_TCP
 * ]
 * ]);
 * $server->start();
 */

