<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/9
 * Time: 下午6:23
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Server;

use FastD\Swoole\Handler\HandlerInterface;
use FastD\Swoole\Manager\Output;
use FastD\Swoole\Manager\ServerManager;
use FastD\Swoole\SwooleInterface;

/**
 * Class Server
 *
 * @package FastD\Swoole\Server
 */
abstract class Server implements ServerInterface
{
    use Output;

    /**
     * @var \swoole_server
     */
    protected $server;

    protected $sock_type;

    protected $host;

    protected $port;

    protected $mode;

    protected $handles = [];

    protected $workspace_dir;

    /**
     * Swoole server pid file path.
     *
     * @var string
     */
    protected $pid_file = 'var/server.pid';

    /**
     * Server running log path.
     *
     * @var string
     */
    protected $log_file = 'run/server.log';

    /**
     * Swoole server process name.
     *
     * @var string
     */
    protected $process_name = 'fd-server';

    /**
     * Swoole server run configuration.
     *
     * @var array
     */
    protected $config = [
        'dispatch_mode' => 2,
        'reactor_num'   => 1,
        'max_conn'      => 1024,
        'max_request'   => 0,
        'task_tmpdir'   => '/tmp/fd_tmp/',
        'user'          => 'www',
        'group'         => 'www',
        'daemonize'     => false,
        'log_level'     => 2,
    ];

    final public function __construct($host, $port, $mode = SwooleInterface::SWOOLE_BASE, $sock_type = null)
    {
        $this->init($host, $port, $mode, $sock_type);
    }

    final protected function init($host, $port, $mode = SwooleInterface::SWOOLE_BASE, $sock_type = null)
    {
        $this->host = $host;

        $this->port = $port;

        $this->mode = $mode;

        $this->workspace_dir = realpath('.');
    }

    final public static function create($host, $port, $mode = SwooleInterface::SWOOLE_BASE, $sock_type = null)
    {
        return new static($host, $port, $mode, $sock_type);
    }

    public function enableAsync()
    {
        $this->sock_type = SWOOLE_SOCK_ASYNC;

        return $this;
    }

    public function enableSync()
    {
        $this->sock_type = SWOOLE_SOCK_SYNC;

        return $this;
    }

    /**
     * @param array $configure
     * @return $this
     */
    public function configure(array $configure)
    {
        if (is_string($configure)) {
            switch(pathinfo($configure, PATHINFO_EXTENSION)) {
                case 'ini':
                    $configure = parse_ini_file($configure);
                    break;
                case 'php':
                default:
                $configure = include $configure;
            }
        }

        $this->config = array_merge($this->config, $configure);
    }

    /**
     * @return int|null
     */
    public function getPid()
    {
        return (int) @file_get_contents($this->getPidFile());
    }

    /**
     * @return $this
     */
    public function daemonize()
    {
        $this->config['daemonize'] = true;

        return $this;
    }

    /**
     * @param      $name
     * @param      $callback
     * @return $this
     */
    public function on($name, $callback)
    {
        $this->handles[$name] = $callback;

        return $this;
    }

    /**
     * @return mixed
     */
    public function start()
    {
        $this->config['log_file'] = $this->getLogFile();

        if (!file_exists(dirname($this->config['log_file']))) {
            mkdir(dirname($this->config['log_file']), 0755, true);
        }

        $this->server->set($this->config);

        if (null === $this->handler) {
            throw new \RuntimeException("Server is not has handler.");
        }

        return $this->server->start();
    }

    public function getServer()
    {
        return $this->server;
    }
}