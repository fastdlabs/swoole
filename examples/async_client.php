<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

use FastD\Swoole\Async\AsyncClient;

include __DIR__ . '/../vendor/autoload.php';

$client = new AsyncClient('tcp://127.0.0.1:9527');

$client->onReceive(function ($client, $data) {
    echo $data . PHP_EOL;
});

$client->onError(function ($client) {
    echo 'error';
});

$client->onClose(function ($client) {
//    $client->close();
    echo 'close';
});

$client->connect(function ($client) {
    $client->send('hello world');
});