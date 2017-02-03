<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

use FastD\Swoole\Client\Socket;

include __DIR__ . '/../../vendor/autoload.php';

$socket = new Socket('tcp://127.0.0.1:9527');

$socket->connect(function (Socket $socket) {
    $socket->send('hello world');
})->receive(function ($data) {
    echo ($data) . PHP_EOL;
})->resolve();

