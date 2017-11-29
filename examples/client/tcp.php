<?php

include __DIR__ . '/../../vendor/autoload.php';

$client = new \FastD\Swoole\Client('tcp://127.0.0.1:9527');
$client->configure([
    'open_eof_check' => true,
    'package_eof' => "\r\n",
    'package_max_length' => 1024 * 1024 * 2,
]);
echo $client->send('hello');


