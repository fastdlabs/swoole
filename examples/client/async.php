<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../../vendor/autoload.php';

class Cli extends \FastD\Swoole\Client
{
    public function onConnect(swoole_client $client)
    {
        $client->send('hello');
    }

    public function onReceive(swoole_client $client, $data)
    {
        echo $data;
        $client->close();
    }
}

$client = new Cli('tcp://127.0.0.1:9527', true);
$client->configure([
    'open_eof_check' => true,
    'package_eof' => "\r\n",
    'package_max_length' => 1024 * 1024 * 2,
]);
$client->start();