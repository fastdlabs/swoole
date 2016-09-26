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

namespace FastD\Swoole\Console;

/**
 * Class Output
 *
 * @package FastD\Swoole\Manager
 */
/**
 * Class Output
 *
 * @package FastD\Swoole\Console
 */
class Output
{
    /**
     * @var Output
     */
    protected static $instance;

    /**
     * @param $msg
     * @return void
     */
    public static function output($msg)
    {
        echo static::format($msg);
    }

    /**
     * @param $msg
     * @return string
     */
    public static function format($msg)
    {
        return sprintf("[%s]\t" . $msg . PHP_EOL, date('Y-m-d H:i:s'));
    }

    /**
     * @param $msg
     */
    public static function flush($msg)
    {
        static::output($msg);
    }
}