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

namespace FastD\Swoole;

/**
 * Interface SwooleHandlerInterface
 *
 * @package FastD\Swoole
 */
interface SwooleHandlerInterface
{
    /**
     * @param array $on
     * @return $this
     */
    public function setPrepareBind(array $on);

    /**
     * @return array
     */
    public function getPrepareBind();

    /**
     * @param SwooleInterface $swooleInterface
     * @return $this
     */
    public function handle(SwooleInterface $swooleInterface);
}