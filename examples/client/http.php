<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2017
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

include __DIR__ . '/../../vendor/autoload.php';

$client = new \FastD\Swoole\Client('http://127.0.0.1:9527');

echo $client->setMethod('POST')->send(['foo' => 'bar']);