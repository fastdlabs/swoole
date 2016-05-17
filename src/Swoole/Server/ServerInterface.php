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

use FastD\Swoole\SwooleInterface;

/**
 * Interface ServerInterface
 *
 * @package FastD\Swoole\Server
 */
interface ServerInterface extends SwooleInterface
{
    /**
     * Run server.
     *
     * @return int
     */
    public function start();
}