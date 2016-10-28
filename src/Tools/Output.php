<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Tools;

trait Output
{
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