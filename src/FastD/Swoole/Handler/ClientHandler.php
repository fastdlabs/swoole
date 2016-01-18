<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/1/18
 * Time: 下午10:43
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Handler;

class ClientHandler implements ClientHandlerInterface
{
    public function onConnect(\swoole_client $client)
    {
        echo 'connect';
    }

    public function onReceive(\swoole_client $client, $data)
    {
        echo 'receive: ' . $data;
    }

    public function onError(\swoole_client $client)
    {
        echo 'error';
    }

    public function onClose(\swoole_client $client)
    {
        echo 'close';
    }
}