<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/12/1
 * Time: 上午12:12
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

include __DIR__ . '/../vendor/autoload.php';

use FastD\Swoole\Server\SwooleServer;
use FastD\Swoole\Handler\ServerHandler;

$server = SwooleServer::create('tcp://127.0.0.1:9501', [], new ServerHandler());

$server->start();


