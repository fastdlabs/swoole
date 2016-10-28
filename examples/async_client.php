<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

use FastD\Swoole\Client\Async\AsyncClient;

include __DIR__ . '/../vendor/autoload.php';

$client = new AsyncClient('tcp://127.0.0.1:9527');

$client
    ->connect(function ($client) {
        $client->send('hello word');
    })
    ->receive(function ($client, $data) {
        echo $data . PHP_EOL;
    })
    ->error(function () {
        echo 'error' . PHP_EOL;
    })
;