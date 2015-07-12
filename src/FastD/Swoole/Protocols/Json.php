<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/12
 * Time: 下午6:17
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Protocols;

use FastD\Swoole\ProtocolInterface;

class Json implements ProtocolInterface
{
    public function decode($data)
    {
        return json_decode($data, true);
    }

    public function encode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}