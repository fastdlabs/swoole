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
    public function getPidPath();

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

    /**
     * Get running server user.
     *
     * @return string
     */
    public function getUser();

    /**
     * @param $user
     * @return $this
     */
    public function setUser($user);

    /**
     * Get running server user group.
     *
     * @return string
     */
    public function getGroup();

    /**
     * @param $group
     * @return $this
     */
    public function setGroup($group);

    /**
     * @param      $name
     * @param null $value
     * @return $this
     */
    public function setConfig($name, $value = null);

    /**
     * @param Context $context
     * @return $this
     */
    public function setContext(Context $context);

    /**
     * @return Context
     */
    public function getContext();

    /**
     * @param      $name
     * @param null $callback
     * @return $this
     */
    public function on($name, $callback = null);

    /**
     * @param SwooleHandlerInterface $swooleHandlerInterface
     * @return $this
     */
    public function handle(SwooleHandlerInterface $swooleHandlerInterface);
}