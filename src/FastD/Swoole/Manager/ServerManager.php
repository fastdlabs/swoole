<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/1/29
 * Time: 下午10:43
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Manager;

use FastD\Swoole\Server\ServerInterface;

/**
 * Server Manager.
 *
 * Class ServerManager
 *
 * @package FastD\Swoole\Manager
 */
class ServerManager
{
    /**
     * @var ServerInterface
     */
    protected $server;

    protected $server_pid;

    protected $server_pid_file;

    protected $server_log_file;

    /**
     * ServerManager constructor.
     *
     * @param ServerInterface $serverInterface
     */
    public function __construct(ServerInterface $serverInterface)
    {
        $this->server = $serverInterface;

        $this->server_pid = $serverInterface->getPid();

        $this->server_pid_file = $serverInterface->getPidFile();

        $this->server_log_file = $serverInterface->getLogFile();
    }

    /**
     * @return array|null
     */
    public function status()
    {
        $pid = $this->getPid();
        if (empty($pid)) {
            echo 'Server [' . $this->getContext()->get('process_name') . '] not running...' . PHP_EOL;
            return 0;
        }
        echo 'Server [' . $this->getContext()->get('process_name') . ' pid: ' . $pid . '] is running...' . PHP_EOL;
        return 0;
    }

    /**
     * @return mixed
     */
    public function shutdown()
    {
        $pid = $this->getPid();

        if (empty($pid)) {
            echo 'Server [' . $this->getContext()->get('process_name') . '] not running...' . PHP_EOL;
            return 1;
        }

        exec("kill -15 {$pid}");
        echo 'Server [' . $this->getContext()->get('process_name') . ' pid: ' . $pid . '] is stop...' . PHP_EOL;
        return 0;
    }

    /**
     * @return mixed
     */
    public function reload()
    {
        $pid = $this->getPid();

        if (empty($pid)) {
            echo 'Server [' . $this->getContext()->get('process_name') . '] not running...' . PHP_EOL;
        }
        exec("kill -USR1 {$pid}");
        echo 'Server [' . $this->getContext()->get('process_name') . ' pid: ' . $pid . '] reload...' . PHP_EOL;

        return 0;
    }

    /**
     * @return int
     */
    public function usage()
    {
        echo 'Usage: Server {start|stop|restart|reload|status} ' . PHP_EOL;
        return 0;
    }
}