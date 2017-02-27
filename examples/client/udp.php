<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../../vendor/autoload.php';

$client = new \FastD\Swoole\Client\Sync\UDP('udp://127.0.0.1:9527');

$client
    ->connect(function ($client) {
        $client->send('hello world');
    })
    ->receive(function ($client, $data) {
        echo $data . PHP_EOL;
        $client->close();
    })
    ->resolve()
;
