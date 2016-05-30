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

include __DIR__ . '/../../vendor/autoload.php';

use FastD\Swoole\Server\TcpServer;
use FastD\Swoole\Console\Service;
use FastD\Swoole\Monitor\Manager;

$server = TcpServer::create();
$manager = new Manager();
$manager
    ->setHost('11.11.11.22')
    ->setPort('9555')
;

$server->on('receive', function (\swoole_server $server, $fd) {
    echo 'receive' . PHP_EOL;
    $server->close($fd);
});

$server->setMonitor($manager);

$action = 'status';

if (isset($_SERVER['argv'][1])) {
    $action = $_SERVER['argv'][1];
}

switch ($action) {
    case 'start':
        Service::server($server)->start();
        break;
    case 'stop':
        Service::server($server)->shutdown();
        break;
    case 'restart':
        Service::server($server)->shutdown();
        Service::server($server)->start();
        break;
    case 'reload':
        Service::server($server)->reload();
        break;
    case 'watch':
        Service::server($server)->watch();
        break;
    case 'status':
    default:
        Service::server($server)->status();
}

