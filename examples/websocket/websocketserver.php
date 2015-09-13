<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/9/13
 * Time: 下午4:25
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

include __DIR__ . '/../../vendor/autoload.php';

$server = FastD\Swoole\WebSocket\WebSocketServer::create('websocket://0.0.0.0:9321', [],
    new \FastD\Swoole\WebSocket\WebSocketHandler()
);

$server->setUser('vagrant');
$server->setGroup('vagrant');
$server->rename('swoole-ws');

$invoker = new \FastD\Swoole\Invoker($server);

$invoker->start();
 