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
    public function output($msg)
    {
        echo $this->format($msg);
    }

    /**
     * @param $msg
     * @return string
     */
    public function format($msg)
    {
        return sprintf("[%s]\t" . $msg . '...' . PHP_EOL, date('Y-m-d H:i:s'));
    }

    /**
     * @param $msg
     */
    public function flush($msg)
    {
        $this->output($msg);
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([static::getInstance(), $name], $arguments);
    }
}