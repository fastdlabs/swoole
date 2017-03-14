<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../../vendor/autoload.php';

use FastD\Swoole\Server\TCP;

class DemoServer extends TCP
{
    public function doConnect(swoole_server $server, $fd, $from_id)
    {
        echo 'onConnect: ' . $fd . PHP_EOL;
        timer_tick(1000, function () use ($server, $fd) {
            $text = Faker\Factory::create()->text;
            $server->send($fd, $text);
            echo $text . PHP_EOL;
        });
    }

    public function doWork(swoole_server $server, $fd, $data, $from_id)
    {
        echo $fd;
        echo $data . PHP_EOL;
        $server->task($data);
        return $data;
    }

    public function doTask(swoole_server $server, $data, $taskId, $workerId)
    {
        echo $data . ' on task' . PHP_EOL;
        return $data;
    }

    public function doFinish(swoole_server $server, $data, $taskId)
    {
        echo $data . 'Finish' . PHP_EOL;
    }
}

$server = DemoServer::createServer('tcp swoole', 'tcp://0.0.0.0:9527', [
    'pid_file' => '/tmp/swoole.pid',
]);

$server->start();
