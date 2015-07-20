<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/20
 * Time: 下午11:08
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Client;

class ClientHandler extends ClientHandlerAbstract
{
    public function onConnect(\swoole_client $client)
    {
        echo 'client connect' . PHP_EOL;
    }

    public function onReceive(\swoole_client $client, $data)
    {
        echo 'client receive' . PHP_EOL;
    }

    public function onError(\swoole_client $client)
    {
        echo 'client error' . PHP_EOL;
    }

    public function onClose(\swoole_client $client)
    {
        echo 'client close' . PHP_EOL;
    }
}