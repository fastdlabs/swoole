<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../../vendor/autoload.php';

$client = new \FastD\Swoole\Coroutine\TCP('127.0.0.1:9527');

$client->send('hello world');

$data = $client->receive();

var_dump($data);

$client->close();