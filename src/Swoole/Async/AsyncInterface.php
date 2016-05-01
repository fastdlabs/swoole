<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/3/9
 * Time: 上午10:36
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Async;

/**
 * 异步处理模块
 *
 * Interface AsyncInterface
 *
 * @package FastD\Swoole\Async
 */
interface AsyncInterface
{
    public function multiQuery();

    public function multiSet();

    public function multiGet();
}