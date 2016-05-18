<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/18
 * Time: 上午12:20
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Manager;

interface MonitorInterface
{
    public function listen($host, $port);
}