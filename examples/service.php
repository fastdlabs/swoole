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

use FastD\Swoole\Server\Server;
use FastD\Swoole\Console\Service;

class Demo extends Server
{
    /**
     * @param \swoole_server $server
     * @param int $fd
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    public function doWork(\swoole_server $server, int $fd, int $from_id, string $data)
    {
        $server->send($fd, $data);
        $server->close($fd);
    }

    /**
     * @param \swoole_server $server
     * @param string $data
     * @param array $client_info
     */
    public function doPacket(\swoole_server $server, string $data, array $client_info)
    {
        // TODO: Implement doPacket() method.
    }
}

$service = Service::server(Demo::class, [

]);

$action = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'status';

switch ($action) {
    case 'status':
        $service->status();
        break;
    case 'start':
        $service->start();
        break;
    case 'stop':
        $service->shutdown();
        break;
    case 'reload':
        $service->reload();
        break;
    case 'watch':
        $service->watch(['.']);
        break;
}
