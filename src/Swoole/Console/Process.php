<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/17
 * Time: 下午11:50
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Console;

/**
 * Class Process
 *
 * @package FastD\Swoole\Console
 */
class Process
{
    /**
     * @param $name
     */
    public static function rename($name)
    {
        // hidden Mac OS error。
        set_error_handler(function () {});

        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($name);
        } else if (function_exists('swoole_set_process_name')) {
            swoole_set_process_name($name);
        }

        restore_error_handler();
    }
}