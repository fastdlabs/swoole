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
include __DIR__ . '/handler.php';

use FastD\Swoole\Server\HttpServer;

$server = HttpServer::create('0.0.0.0', '9321');

$dir = realpath(__DIR__ . '/..') . '/ssl';

$server->enableSSL(
    $dir . '/server.crt',
    $dir . '/server.key'
);

$server->enableHttp2();

$server->handle(new HttpHandler());

$server->start();