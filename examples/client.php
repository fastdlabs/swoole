<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/12
 * Time: 下午4:25
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

include __DIR__ . '/../vendor/autoload.php';

use FastD\Swoole\Client\Client as Client;
use FastD\Swoole\Client\ClientHandler as Handler;

$client = new Client();

$client->connect('tcp://127.0.0.1:9321');

$client->send('hello world');

$receive = $client->receive();

$client->close();

echo $receive;