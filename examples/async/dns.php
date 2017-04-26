<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

include __DIR__ . '/../../vendor/autoload.php';

$dns = new \FastD\Swoole\AsyncIO\DNS('www.sina.com.cn');

$dns->lookup(function ($host, $ip) {
    echo "{$host} reslove to {$ip}\n";
});

