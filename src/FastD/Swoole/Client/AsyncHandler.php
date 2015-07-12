<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/12
 * Time: 下午5:16
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Client;

class AsyncHandler extends ClientHandler
{
    public function onConnect(\swoole_client $client)
    {
        echo 'connect';
    }

    public function onReceive(\swoole_client $client, $data)
    {
        echo 'receive';
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