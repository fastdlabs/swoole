<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../vendor/autoload.php';

class Server extends \FastD\Swoole\Server\Tcp
{
    /**
     * @param swoole_server $server
     * @param $fd
     * @param $data
     * @param $from_id
     * @return mixed
     */
    public function doWork(swoole_server $server, $fd, $data, $from_id)
    {
        return 'hello server1';
    }
}

class Server2 extends \FastD\Swoole\Server\Udp
{
    /**
     * @param swoole_server $server
     * @param $data
     * @param $client_info
     * @return mixed
     */
    public function doPacket(swoole_server $server, $data, $client_info)
    {
        return 'hello server2';
    }
}

$server = new Server('tcp server', 'tcp://127.0.0.1:9527');

$server->listen(new Server2('tcp server2', 'udp://127.0.0.1:9528'));

$server->start();

