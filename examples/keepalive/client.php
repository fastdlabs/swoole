<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../../vendor/autoload.php';

$client = new \FastD\Swoole\Client('tcp://127.0.0.1:9527', false, true);

for ($i = 0; $i< 10; $i++) {
    echo $client->send('hello', false, true) . PHP_EOL;
}




