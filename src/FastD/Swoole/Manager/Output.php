<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/2/14
 * Time: 下午9:58
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Manager;

trait Output
{
    /**
     * @param $msg
     * @return void
     */
    public function output($msg)
    {
        echo sprintf("[%s]\t" . $msg . '...' . PHP_EOL, date('Y-m-d H:i:s'));
    }
}