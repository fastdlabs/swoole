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
 * Interface SwooleInterface
 *
 * @package FastD\Swoole
 */
interface SwooleInterface
{
    const LOGO = <<<LOGO
    ____    __
   / __/___/ /
  / /_/ __  /
 / __/ /_/ /
/_/  \__,_/
LOGO;

    // server mode
    const SWOOLE_BASE      = SWOOLE_BASE;
    const SWOOLE_PROCESS   = SWOOLE_PROCESS;
    const SWOOLE_THREAD    = SWOOLE_THREAD;

    const SWOOLE_SOCK_TCP   = SWOOLE_SOCK_TCP;
    const SWOOLE_SOCK_UDP   = SWOOLE_SOCK_UDP;
    const SWOOLE_SOCK_ASYNC = SWOOLE_SOCK_ASYNC;
    const SWOOLE_SOCK_SYNC  = SWOOLE_SOCK_SYNC;

    const SWOOLE_TCP = SWOOLE_TCP;
    const SWOOLE_UDP = SWOOLE_UDP;

    /**
     * @param $name
     * @param $callback
     * @return $this
     */
    public function on($name, $callback);

    /**
     * @param array $configure
     * @return $this
     */
    public function configure(array $configure);

    /**
     * @return mixed
     */
    public function start();

    /**
     * @return mixed
     */
    public function reload();

    /**
     * @return mixed
     */
    public function shutdown();
}