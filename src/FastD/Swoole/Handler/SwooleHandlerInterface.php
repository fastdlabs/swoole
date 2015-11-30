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

use FastD\Swoole\Server\SwooleServerInterface;

/**
 * Interface SwooleHandlerInterface
 *
 * @package FastD\Swoole\Handler
 */
interface SwooleHandlerInterface
{
    /**
     * @param SwooleServerInterface $swooleServerInterface
     * @return mixed
     */
    public function handle(SwooleServerInterface $swooleServerInterface);
}