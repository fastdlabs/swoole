<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../vendor/autoload.php';

$queue = new \FastD\Swoole\Queue(function ($worker) {
    while (true) {
        $recv = $worker->pop();
        echo "From Master: $recv\n";
    }
});

$queue->start();

while (true) {
    $queue->push('hello');
    sleep(1);
}


$queue->wait(function ($ret) {
    echo 'PID: ' . $ret['pid'];
});
