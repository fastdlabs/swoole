<?php

use FastD\Swoole\Client\Sync\SyncClient;

include __DIR__ . '/../vendor/autoload.php';

$client = new SyncClient('tcp://127.0.0.1:9527');

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


