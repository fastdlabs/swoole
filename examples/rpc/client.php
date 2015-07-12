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

$client = new \FastD\Swoole\Rpc\RpcClient();

$client->setProtocol(new \FastD\Swoole\Protocols\Json());

$client->connect('tcp://127.0.0.1:9222');

$client->send('hello', ['janhuang']);

$result = $client->receive();

$client->close();

var_dump($result);
