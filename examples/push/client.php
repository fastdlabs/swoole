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
    public function receive(swoole_client $client, $data)
    {
        echo $data;
    }

    public function error(swoole_client $client)
    {

    }

    public function connect(swoole_client $client)
    {
        echo 'connect';
    }

    public function close(swoole_client $client)
    {

    }
}

$client = new Cli('tcp://127.0.0.1:9527', true);

$client->start();

