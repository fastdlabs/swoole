<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../../vendor/autoload.php';

$client = new \FastD\Swoole\Client\Sync\UDP('tcp://127.0.0.1:9527');

echo $client->send('hello');
