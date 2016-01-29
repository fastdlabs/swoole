<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/1/29
 * Time: 下午11:55
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole;

/**
 * Base Swoole Server Interface.
 *
 * Interface SwooleInterface
 *
 * @package FastD\Swoole
 */
interface SwooleInterface
{
    const SWOOLE_MODE_BASE = SWOOLE_BASE;
    const SWOOLE_MODE_PROCESS = SWOOLE_PROCESS;

    const SWOOLE_SOCK_TCP = SWOOLE_SOCK_TCP;
    const SWOOLE_SOCK_UDP = SWOOLE_SOCK_UDP;

    const SWOOLE_ASYNC = SWOOLE_SOCK_ASYNC;
    const SWOOLE_SYNC = SWOOLE_SOCK_SYNC;

    /**
     * @param $name
     * @param $callback
     * @return mixed
     */
    public function on($name, $callback);
}