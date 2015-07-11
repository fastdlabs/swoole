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

/**
 * Class Invoker
 *
 * @package FastD\Swoole
 */
class Invoker
{
    /**
     * @var Swoole
     */
    protected $swoole;

    /**
     * @param Swoole $swoole
     */
    public function __construct(Swoole $swoole)
    {
        $this->swoole = $swoole;
    }

    /**
     * @return int
     */
    public function start()
    {
        return $this->swoole->start();
    }

    public function status()
    {}

    public function stop()
    {}

    public function reload()
    {}
}