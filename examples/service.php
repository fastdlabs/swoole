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
use FastD\Swoole\Console\Service;

/**
 * Class Demo
 */
class Demo extends Server
{
    /**
     * @param Request $request
     * @return string
     */
    public function doWork(Request $request)
    {
        return 'hello service';
    }

    /**
     * @param Request $request
     * @return string
     */
    public function doPacket(Request $request)
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
        $service->watch(['./watch']);
        break;
}
