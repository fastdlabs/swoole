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

class Process
{
    public function rename($name)
    {
        try {
            if (function_exists('cli_set_process_title')) {
                cli_set_process_title($name);
            } else if (function_exists('swoole_set_process_name')) {
                swoole_set_process_name($name);
            }
        } catch (\Exception $e) {}
    }
}