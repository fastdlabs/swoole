<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/1/18
 * Time: 下午10:21
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Server;

class ServerFactory
{
    public static function factory($protocol, $mode = ServerInterface::SERVER_MODE_BASE, $sock = ServerInterface::SERVER_SOCK_TCP)
    {
        parse_url($protocol);
    }
}