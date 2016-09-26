<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/1/18
 * Time: 下午9:47
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

include __DIR__ . '/../vendor/autoload.php';

use FastD\Swoole\Request;
use FastD\Swoole\Server;

/**
 * Class DemoServer
 */
class DemoServer extends Server
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
        return 'hello tcp';
    }

    /**
     * @param swoole_server $server
     * @param $data
     * @param $client_info
     * @return mixed
     */
    public function doPacket(swoole_server $server, $data, $client_info)
    {
        // TODO: Implement doPacket() method.
    }
}

DemoServer::run([
    'ports' => [
        [
            'host' => '127.0.0.1',
            'port' => '9528',
            'sock' => SWOOLE_SOCK_UDP
        ]
    ]
]);

/**
 * 以上写法和以下写法效果一致
 *
 * $test = new DemoServer();
 * $test->start();
 */
