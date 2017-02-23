<?php

use FastD\Swoole\Client\Sync\SyncClient;

include __DIR__ . '/../../vendor/autoload.php';

$client = new SyncClient('udp://127.0.0.1:9528', SWOOLE_SOCK_UDP);

$client
    ->connect(function ($client) {
        $client->send('hello world');
    })
    ->receive(function ($client, $data) {
        echo $data . PHP_EOL;
    })
    ->resolve()
;


