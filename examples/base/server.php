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

$server = TcpServer::create('0.0.0.0', '9321');

$listener = $server->listen('127.0.0,1', '9322');
$listener->on('receive', function (swoole_server $server) use ($server) {
    print_r($server->getServer()->connections);
});

$server->on('receive', function () {
    echo 'receive' . PHP_EOL;
});

$server->start();