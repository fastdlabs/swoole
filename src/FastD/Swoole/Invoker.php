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
        $pid = $this->swoole->getPid();
        if (empty($pid)) {
            echo "Server [' . $this->swoole->getContext()->get('process_name') . '] not running..." . PHP_EOL;
            return 0;
        }
        echo 'Server [' . $this->swoole->getContext()->get('process_name') . ' pid: ' . $pid . '] is running...' . PHP_EOL;
        return 0;
    }

    /**
     * @return mixed
     */
    public function stop()
    {
        $pid = $this->swoole->getPid();

        if (empty($pid)) {
            echo 'Server [' . $this->swoole->getContext()->get('process_name') . '] not running...' . PHP_EOL;
            return 1;
        }

        exec("kill -15 {$pid}");
        echo 'Server [' . $this->swoole->getContext()->get('process_name') . ' pid: ' . $pid . '] is stop...' . PHP_EOL;
        return 0;
    }

    /**
     * @return mixed
     */
    public function reload()
    {
        $pid = $this->swoole->getPid();

        if (empty($pid)) {
            echo 'Server [' . $this->swoole->getContext()->get('process_name') . '] not running...' . PHP_EOL;
        }
        exec("kill -USR1 {$pid}");
        echo 'Server [' . $this->swoole->getContext()->get('process_name') . ' pid: ' . $pid . '] reload...' . PHP_EOL;

        return 0;
    }

    public function usage()
    {
        echo 'Usage: Server {start|stop|restart|reload|status} ' . PHP_EOL;
        return 0;
    }
}