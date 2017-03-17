<?php

include __DIR__ . '/../../vendor/autoload.php';

/**
 * Class DemoServer
 */
class DemoServer extends \FastD\Swoole\Server\UDP
{
    /**
     * @param swoole_server $server
     * @param $data
     * @param $clientInfo
     * @return mixed
     */
    public function doPacket(swoole_server $server, $data, $clientInfo)
    {
        echo $data . PHP_EOL;
        $server->sendto($clientInfo['address'], $clientInfo['port'], $data);
    }
}

return DemoServer::createServer('udp swoole', 'udp://127.0.0.1:9527');

/**
 * 以上写法和以下写法效果一致
 *
 * $test = new DemoServer();
 * $test->start();
 */
