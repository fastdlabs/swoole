<?php

/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/12
 * Time: 下午4:14
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Handler;

/**
 * Interface ServerHandlerInterface
 *
 * @package FastD\Swoole\Handler
 */
interface ServerHandlerInterface extends SwooleHandlerInterface
{
    /**
     * @return array
     */
    public function registerHandles();
}