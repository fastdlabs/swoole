<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/3/7
 * Time: 下午5:52
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

include __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/handle.php';
include __DIR__ . '/api/demo.php';

use FastD\Swoole\Server\RpcServer;

$server = RpcServer::create('0.0.0.0', '9501');

$demo = new Demo();

$server->add('/test', [$demo, 'emptyArg']);

$server
    ->handle(new RpcHandler())
    ->start()
;