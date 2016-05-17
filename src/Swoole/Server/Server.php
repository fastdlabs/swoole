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

use FastD\Swoole\Manager\Output;
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

    /**
     * Server constructor.
     * @param $host
     * @param $port
     * @param int $mode
     * @param int $sock_type
     */
    final public function __construct($host, $port, $mode = SwooleInterface::SWOOLE_BASE, $sock_type = SwooleInterface::SWOOLE_SOCK_TCP)
    {
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
        $this->host = $host;

        $this->port = $port;

        $this->mode = $mode;

        $this->server = new \swoole_server($host, $port, $mode, $sock_type);

        $this->workspace_dir = realpath('.');
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
     * @return $this
     */
    public function daemonize()
    {
        $this->config['daemonize'] = true;

        return $this;
    }

    /**
     * @return void
     */
    public function start()
    {
        $this->server->set($this->config);
        foreach ($this->handles as $name => $handle) {
            $this->server->on($name, $handle);
        }

        $this->server->start();
    }
}