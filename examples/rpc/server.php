<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/12
 * Time: 下午6:00
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

include __DIR__ . '/../../vendor/autoload.php';

$server = FastD\Swoole\Rpc\RpcServer::create(
    'tcp://127.0.0.1:9222',
    [],
    new \FastD\Swoole\Rpc\RpcHandler()
);

$server->setProtocol(new \FastD\Swoole\Protocols\Json());

$server->addCallback('hello', function ($name) {
    return 'hello ' . $name;
});

$server->start();

 