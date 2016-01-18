<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/1/18
 * Time: 下午10:34
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Handler;

interface ClientHandlerInterface
{
    public function onConnect(\swoole_client $client);

    public function onReceive(\swoole_client $client, $data);

    public function onError(\swoole_client $client);

    public function onClose(\swoole_client $client);
}