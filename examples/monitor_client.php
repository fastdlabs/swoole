<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../vendor/autoload.php';

use FastD\Swoole\Client\Client;
use FastD\Packet\Binary;

$client = new Client(SWOOLE_SOCK_UDP);

$client->connect('127.0.0.1', '9527');

$data = $client->send(Binary::encode([
    'cmd' => 'report',
    'data' => [
        'name' => 'test'
    ]
]));

print_r($data);
