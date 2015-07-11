<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/11
 * Time: 上午9:46
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole;

class Invoker
{
    protected $swoole;

    public function __construct(Swoole $swoole)
    {
        $this->swoole = $swoole;
    }

    public function start()
    {

    }

    public function status()
    {}

    public function stop()
    {}

    public function reload()
    {}
}