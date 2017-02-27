<?php

include __DIR__ . '/../../vendor/autoload.php';

$client = new \FastD\Swoole\Client\Sync\TCP('tcp://127.0.0.1:9527');

echo $client->send('hello', true);


