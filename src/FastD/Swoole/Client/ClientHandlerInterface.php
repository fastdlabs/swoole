<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/12
 * Time: 下午4:29
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Client;

use FastD\Swoole\SwooleHandlerInterface;

interface ClientHandlerInterface extends SwooleHandlerInterface
{
    public function onConnect(\swoole_client $client);

    public function onReceive(\swoole_client $client, $data);

    public function onError(\swoole_client $client);

    public function onClose(\swoole_client $client);
}