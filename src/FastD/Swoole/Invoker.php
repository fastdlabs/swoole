<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/11
 * Time: 上午9:46
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole;

/**
 * Class Invoker
 *
 * @package FastD\Swoole
 */
class Invoker
{
    /**
     * @var Swoole
     */
    protected $swoole;

    /**
     * @param Swoole $swoole
     */
    public function __construct(Swoole $swoole)
    {
        $this->swoole = $swoole;
    }

    /**
     * @return int
     */
    public function start()
    {
        return $this->swoole->start();
    }

    /**
     * @return array|null
     */
    public function status()
    {
        return $this->swoole->status();
    }

    /**
     * @return mixed
     */
    public function stop()
    {
        $swoole = $this->swoole->getLastSwoole();

        set_error_handler(function ($code, $message, $file, $line) use ($swoole) {
            if (function_exists('shell_exec')) {
                shell_exec('kill -15 ' . $swoole->pid);
            }
            echo 'server pid:' . $swoole->pid . ' stop...' . PHP_EOL;
        });

        $result = $this->swoole->stop();

        restore_error_handler();

        return $result;
    }

    /**
     * @return mixed
     */
    public function reload()
    {
        $swoole = $this->swoole->getLastSwoole();

        set_error_handler(function ($code, $message, $file, $line) use ($swoole) {
            if (function_exists('shell_exec')) {
                shell_exec('kill -USR1 ' . $swoole->pid);
            }
            echo 'server pid:' . $swoole->pid . ' reloading...' . PHP_EOL;
        });

        $result = $this->swoole->reload();

        restore_error_handler();

        return 0;
    }
}