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
use FastD\Swoole\Monitor\Monitor;
use FastD\Swoole\SwooleInterface;
use FastD\Swoole\Handler\HandlerAbstract;
use FastD\Swoole\Monitor\Manager;
use swoole_server;

/**
 * Class Server
 *
 * @package FastD\Swoole\Server
 */
class Server implements ServerInterface
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
     * @var Manager
     */
    protected $manager;

    /**
     * @var bool
     */
    protected $booted = false;

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
    final public function __construct($host, $port, $mode = SWOOLE_BASE, $sock_type = SWOOLE_SOCK_TCP)
    {
        $this->workspace_dir = isset($_SERVER['PWD']) ? $_SERVER['PWD'] : realpath('.');

        $this->host = $host;
        $this->port = $port;
        $this->mode = $mode;
        $this->sock = $sock_type;

        $this->bootstrap();
    }

    /**
     * Bootstrap server.
     *
     * @return $this
     */
    public function bootstrap()
    {
        $this->handle(new Handle());

        $this->server = $this->initServer();

        $this->booted = true;

        return $this;
    }

    /**
     * @return swoole_server
     */
    public function initServer()
    {
        return new swoole_server($this->host, $this->port, $this->mode, $this->sock);
    }

    /**
     * @return boolean
     */
    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * @return string
     */
    public function getProcessName()
    {
        return static::SERVER_NAME;
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
     * @return int
     */
    public function getPid()
    {
        return (int) trim(@file_get_contents($this->getPidFile()));
    }

    /**
     * @return array
     */
    public function getHandles()
    {
        return $this->handles;
    }

    /**
     * @param $host
     * @param $port
     * @param int $mode
     * @param int $sock_type
     * @return static
     */
    final public static function create($host, $port, $mode = SWOOLE_BASE, $sock_type = SWOOLE_SOCK_TCP)
    {
        return new static($host, $port, $mode, $sock_type);
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
        if (!$this->isBooted()) {
            $this->bootstrap();
        }

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
     * Shutdown server
     *
     * @return void
     */
    public function shutdown()
    {
        $this->server->shutdown();
    }

    /**
     * @return mixed
     */
    public function status()
    {
        return $this->server->stats();
    }
}