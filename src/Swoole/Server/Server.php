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

use FastD\Swoole\Console\Output;
use FastD\Swoole\SwooleInterface;
use FastD\Swoole\Handler\HandlerAbstract;

/**
 * Class Server
 *
 * @package FastD\Swoole\Server
 */
abstract class Server implements ServerInterface
{
    /**
     * @var \swoole_server
     */
    protected $server;

    /**
     * @var array
     */
    protected $handles = [];

    /**
     * @var string
     */
    protected $workspace_dir;

    /**
     * Swoole server pid file path.
     *
     * @var string
     */
    protected $pid_file = 'var/server.pid';

    /**
     * Swoole server run configuration.
     *
     * @var array
     */
    protected $config = [
        'dispatch_mode' => 2,
        'reactor_num'   => 1,
        'worker_num'    => 1,
        'max_conn'      => 1024,
        'max_request'   => 0,
        'task_tmpdir'   => '/tmp/fd/',
        'user'          => 'nobody',
        'group'         => 'nobody',
        'daemonize'     => false,
        'log_level'     => 2,
        'log_file'      => 'run/server.log'
    ];

    /**
     * Server constructor.
     * @param $host
     * @param $port
     * @param int $mode
     * @param int $sock_type
     */
    final public function __construct($host, $port, $mode = SwooleInterface::SWOOLE_BASE, $sock_type = SwooleInterface::SWOOLE_SOCK_TCP)
    {
        $this->workspace_dir = isset($_SERVER['PWD']) ? $_SERVER['PWD'] : realpath('.');

        $this->config['log_file'] = str_replace('//', '/' , $this->workspace_dir . DIRECTORY_SEPARATOR . $this->config['log_file']);
        $this->pid_file = str_replace('//', '/' , $this->workspace_dir . DIRECTORY_SEPARATOR . $this->pid_file);

        $this->init($host, $port, $mode, $sock_type);
    }

    /**
     * @param $host
     * @param $port
     * @param int $mode
     * @param int $sock_type
     */
    public function init($host, $port, $mode = SwooleInterface::SWOOLE_BASE, $sock_type = SwooleInterface::SWOOLE_SOCK_TCP)
    {
        $this->server = new \swoole_server($host, $port, $mode, $sock_type);
    }

    /**
     * @param $host
     * @param $port
     * @param int $mode
     * @param int $sock_type
     * @return static
     */
    final public static function create($host, $port, $mode = SwooleInterface::SWOOLE_BASE, $sock_type = SwooleInterface::SWOOLE_SOCK_TCP)
    {
        return new static($host, $port, $mode, $sock_type);
    }

    /**
     * @return \swoole_server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getWorkSpace()
    {
        return $this->workspace_dir;
    }

    /**
     * @return string
     */
    public function getPidFile()
    {
        return $this->pid_file;
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

        if (isset($this->config['log_file']) && substr($this->config['log_file'], 0, 1) != '/') {
            $this->config['log_file'] = str_replace('//', '/' , $this->workspace_dir . DIRECTORY_SEPARATOR . $this->config['log_file']);
        }

        if (isset($this->config['pid_file'])) {
            if (substr($this->config['pid_file'], 0, 1) != '/') {
                $this->pid_file = str_replace('//', '/' , $this->workspace_dir . DIRECTORY_SEPARATOR . $this->config['pid_file']);
            } else {
                $this->pid_file = $this->config['pid_file'];
            }
            unset($this->config['pid_file']);
        }
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
     * @param HandlerAbstract $handlerAbstract
     * @return mixed
     */
    public function handle(HandlerAbstract $handlerAbstract)
    {
        $handlerAbstract->handle($this);
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
     * @return mixed
     */
    public function start()
    {
        $this->server->set($this->config);

        foreach ($this->handles as $name => $handle) {
            $this->server->on($name, $handle);
        }

        $this->server->start();
    }

    /**
     * @return mixed
     */
    public function reload()
    {
        $this->server->reload();
    }

    /**
     * @return mixed
     */
    public function shutdown()
    {
        $this->server->shutdown();
    }
}