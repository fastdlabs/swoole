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

    /**
     * @var int
     */
    protected $server_pid;

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
     * Bind running server in manager.
     *
     * @param ServerInterface $serverInterface
     * @return $this
     */
    public function bindServer(ServerInterface $serverInterface)
    {
        $this->server = $serverInterface;

        $this->server_pid = $serverInterface->getPid();

        return $this;
    }

    /**
     * @param array         $directories
     * @param \Closure|null $callback
     * @throws \Exception
     */
    public function watch(array $directories, \Closure $callback = null)
    {
        $watch = new Watcher();

        $self = $this;

        try {
            $watch
                ->watch($directories, $callback ? $callback : function (Watcher $watcher) use ($self) {
                    $self->reload();
                    $watcher->output('Reload finish');
                })
                ->run()
            ;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Start server
     *
     * @return int
     */
    public function start()
    {
        if ($this->server instanceof ServerInterface) {
            $this->server->start();
            return 0;
        }

        throw new \RuntimeException('Unbind server.');
    }

    /**
     * Show server status.
     *
     * @return array|null
     */
    public function status()
    {
        if (empty($this->server_pid)) {
            $this->output('Status: Server is not running');
            return 0;
        }

        $this->output('Status: Server [Pid: ' . $this->server_pid . '] is running');
        return 0;
    }

    /**
     * @return int
     */
    public function shutdown()
    {
        if (empty($this->server_pid)) {
            $this->output('Shutdown: Server is not running');
            return -1;
        }

        posix_kill($this->server_pid, SIGTERM);

        $this->output('Shutdown: Server [Pid: ' . $this->server_pid . '] is shutdown');
        return 0;
    }

    /**
     * @return mixed
     */
    public function reload()
    {
        if (empty($this->server_pid)) {
            $this->output('Reload: Server is not running');
            return 0;
        }

        posix_kill($this->server_pid, SIGUSR1);

        $this->output('Reload: Server [Pid: ' . $this->server_pid . '] reload');
        return 0;
    }

    /**
     * @return int
     */
    public function restart()
    {
        $this->shutdown();
        $this->start();

        return 0;
    }

    /**
     * @return int
     */
    public function usage()
    {
        $this->output('Usage: Server {start|stop|restart|reload|status}');
        return 0;
    }

    /**
     * @return int
     */
    public function tree()
    {
        return 0;
    }
}