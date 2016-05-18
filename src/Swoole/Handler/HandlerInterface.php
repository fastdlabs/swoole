<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/11
 * Time: 上午10:02
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Handler;

use FastD\Swoole\Server\Server;

/**
 * Interface HandlerInterface
 *
 * @package FastD\Swoole\Handler
 */
interface HandlerInterface
{
    /**
     * Handle server.
     * The method of dealing with the first two letters to on.
     *
     * @param Server $server
     * @return mixed
     */
    public function handle(Server $server);
}