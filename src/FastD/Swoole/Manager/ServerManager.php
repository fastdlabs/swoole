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

    protected $server_name = 'fd-server';

    /**
     * ServerManager constructor.
     *
     * @param $pid
     */
    public function __construct($pid = null)
    {
        $this->setPid($pid);
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->server_pid;
    }

    /**
     * @param int $pid
     * @return $this
     */
    public function setPid($pid)
    {
        $this->server_pid = $pid;

        return $this;
    }

    /**
     * @param ServerInterface $serverInterface
     * @return $this
     */
    public function bind(ServerInterface $serverInterface)
    {
        $this->server = $serverInterface;

        $this->server_pid = $serverInterface->getPid();

        $this->server_name = $serverInterface->getName();

        return $this;
    }

    public function start()
    {
        if ($this->server instanceof ServerInterface) {
            $this->server->start();
            return 0;
        }

        throw new \RuntimeException('Unbind server.');
    }

    /**
     * @param array         $directories
     * @param \Closure|null $callback
     * @throws \Exception
     */
    public function watch(array $directories, \Closure $callback = null)
    {
        $watch = new Watcher();

        try {
            $watch
                ->watch($directories, $callback)
                ->run()
            ;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Show server status.
     *
     * @return array|null
     */
    public function status()
    {
        if (empty($this->server_pid)) {
            echo 'Server [' . $this->server_name . '] not running...' . PHP_EOL;
            return 0;
        }
        echo 'Server [' . $this->server_name . ' pid: ' . $this->server_pid . '] is running...' . PHP_EOL;
        return 0;
    }

    /**
     * @return mixed
     */
    public function shutdown()
    {
        if (empty($this->server_pid)) {
            echo 'Server [' . $this->server_name . '] not running...' . PHP_EOL;
            return 1;
        }

        posix_kill($this->server_pid, SIGTERM);
        echo 'Server [' .  $this->server_name . ' pid: ' . $this->server_pid . '] is stop...' . PHP_EOL;
        return 0;
    }

    /**
     * @return mixed
     */
    public function reload()
    {
        if (empty($this->server_pid)) {
            echo 'Server [' . $this->server_name . '] not running...' . PHP_EOL;
        }

        posix_kill($this->server_pid, SIGUSR1);
        echo 'Server [' . $this->server_name . ' pid: ' . $this->server_pid . '] reload...' . PHP_EOL;

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

    public function getUsage()
    {}

    public function getTree()
    {

    }
}