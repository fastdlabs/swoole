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

use FastD\Swoole\Handler\Handle;
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
    protected $pid_file;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var int
     */
    protected $mode;

    /**
     * @var int
     */
    protected $sock;

    /**
     * Swoole server run configuration.
     *
     * @var array
     */
    protected $config = [
        'log_level' => 2,
        'log_file' => 'var/' . Server::SERVER_NAME . '.log',
    ];

    /**
     * Server constructor.
     * @param $host
     * @param $port
     * @param int $mode
     * @param int $sock_type
     */
    final public function __construct($host = null, $port = null, $mode = null, $sock_type = null)
    {
        $this->workspace_dir = isset($_SERVER['PWD']) ? $_SERVER['PWD'] : realpath('.');

        $this->configure($this->workspace_dir . '/etc/server.ini');

        $this->host = null === $host ? $this->host : $host;
        $this->port = null === $port ? $this->port : $port;
        $this->mode = null === $mode ? $this->mode : $mode;
        $this->sock = null === $sock_type ? $this->sock : $sock_type;

        $this->init($this->host, $this->port, $this->mode, $this->sock);
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

        $this->handle(new Handle());
    }

    /**
     * @param $host
     * @param $port
     * @param int $mode
     * @param int $sock_type
     * @return static
     */
    final public static function create($host = null, $port = null, $mode = SwooleInterface::SWOOLE_BASE, $sock_type = SwooleInterface::SWOOLE_SOCK_TCP)
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
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
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
    public function getLogFile()
    {
        return $this->config['log_file'];
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
    public function configure($configure)
    {
        if (is_string($configure)) {
            switch(pathinfo($configure, PATHINFO_EXTENSION)) {
                case 'ini':
                    $configure = parse_ini_file($configure, true);
                    $configure = $configure[static::NAME];
                    $this->host = $configure['server']['host'] ?? '127.0.0.1';
                    $this->port = $configure['server']['port'] ?? 9501;
                    $this->mode = $configure['server']['mode'] ?? SwooleInterface::SWOOLE_PROCESS;
                    $this->sock = $configure['server']['sock'] ?? SwooleInterface::SWOOLE_SOCK_TCP;
                    $this->pid_file = $configure['server']['pid'] ?? 'run/' . Server::SERVER_NAME . '.pid';
                    break;
                case 'php':
                default:
                $configure = include $configure;
            }
        }

        $this->config = array_merge($this->config, $configure);

        if (substr($this->pid_file, 0, 1) != '/') {
            $this->pid_file = str_replace('//', '/' , $this->workspace_dir . DIRECTORY_SEPARATOR . $this->pid_file);
        }

        if (substr($this->config['log_file'], 0, 1) != '/') {
            $this->config['log_file'] = str_replace('//', '/' , $this->workspace_dir . DIRECTORY_SEPARATOR . $this->config['log_file']);
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

    /**
     * @return void
     */
    public function reload()
    {
        $this->server->reload();
    }

    /**
     * @return void
     */
    public function shutdown()
    {
        $this->server->shutdown();
    }

    /**
     * @return void
     */
    public function stop()
    {
        $this->server->stop();
    }
}