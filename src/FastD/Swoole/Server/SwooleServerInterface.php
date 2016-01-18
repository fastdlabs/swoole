<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/10
 * Time: 上午11:55
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Server;

use FastD\Swoole\Context;
use FastD\Swoole\Handler\SwooleHandlerInterface;

/**
 * Interface SwooleServerInterface
 *
 * @package FastD\Swoole\Server
 */
interface SwooleServerInterface
{
    const SERVER_MODE_BASE = SWOOLE_BASE;
    const SERVER_MODE_PROCESS = SWOOLE_PROCESS;

    const SERVER_SOCK_TCP = SWOOLE_SOCK_TCP;
    const SERVER_SOCK_UDP = SWOOLE_SOCK_UDP;

    /**
     * Get server pid
     *
     * @return int
     */
    public function getPid();

    /**
     * Get server pid file absolute path.
     *
     * @return string
     */
    public function getPidFile();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getLogFile();

    /**
     * @param      $name
     * @param      $callback
     * @return $this
     */
    public function on($name, $callback);

    /**
     * @param SwooleHandlerInterface $swooleHandlerInterface
     * @return $this
     */
    public function handle(SwooleHandlerInterface $swooleHandlerInterface);

    /**
     * Run server.
     *
     * @return int
     */
    public function start();

    /**
     * Get server running status.
     *
     * @return string
     */
    public function status();

    /**
     * Shutdown running server.
     *
     * @return int
     */
    public function shutdown();
}