<?php

include __DIR__ . '/../vendor/autoload.php';

/**
 * Class DemoServer
 */
class DemoServer extends \FastD\Swoole\Server\Udp
{
    /**
     * @param swoole_server $server
     * @param $data
     * @param $client_info
     * @return mixed
     */
    public function doPacket(swoole_server $server, $data, $client_info)
    {
        echo $data . PHP_EOL;
        return 'hello tcp';
    }
}

DemoServer::run('tcp://127.0.0.1:9527');

/**
 * 以上写法和以下写法效果一致
 *
 * $test = new DemoServer();
 * $test->start();
 */
