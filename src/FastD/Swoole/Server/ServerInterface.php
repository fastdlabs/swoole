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

use FastD\Swoole\Handler\HandlerInterface;
use FastD\Swoole\SwooleInterface;

/**
 * Interface ServerInterface
 *
 * @package FastD\Swoole\Server
 */
interface ServerInterface extends SwooleInterface
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
     * @param HandlerInterface $handlerInterface
     * @return $this
     */
    public function handle(HandlerInterface $handlerInterface);

    /**
     * Run server.
     *
     * @return int
     */
    public function start();
}