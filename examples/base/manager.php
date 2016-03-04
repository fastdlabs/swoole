<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/2/14
 * Time: 下午5:03
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

include __DIR__ . '/../../vendor/autoload.php';

use FastD\Swoole\Manager\ServerManager;
use FastD\Swoole\Server\Server;

$manager = new ServerManager();

$manager->bindServer(Server::create('0.0.0.0', '9321'));

$action = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'usage';

switch ($action) {
    case 'start':
        $manager->start();
        break;
    case 'status':
        $manager->status();
        break;
    case 'stop':
        $manager->shutdown();
        break;
    case 'restart':
        $manager->restart();
        break;
    case 'reload':
        $manager->reload();
        break;
    case 'tree':
        $manager->tree();
        break;
    default:
        $manager->usage();
}