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
class Server implements ServerInterface
{
    use Output;

    /**
     * @var \swoole_server
     */
    protected $server;

    /**
     * Swoole server pid file path.
     *
     * @var string
     */
    protected $pid_file = '/tmp/{name}/var/server.pid';

    /**
     * Server running log path.
     *
     * @var string
     */
    protected $log_file = '/tmp/{name}/var/server.log';

    /**
     * Swoole server process name.
     *
     * @var string
     */
    protected $process_name = 'fd-server';

    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * @var bool
     */
    protected $daemonize = false;

    /**
     * @var ServerManager
     */
    protected $manager;

    /**
     * Swoole server run configuration.
     *
     * @var array
     */
    protected $config = [
        'dispatch_mode'         => 2,
        'reactor_num'           => 1,
        'max_conn'              => 1024,
        'max_request'           => 0,
        'task_tmpdir'           => '/tmp/fd_tmp/',
        'user'                  => 'www',
        'group'                 => 'www',
        'daemonize'             => false,
    ];

    /**
     * Server constructor.
     *
     * @param $host
     * @param $port
     * @param $mode
     * @param $sock
     */
    public function __construct($host, $port, $mode = SwooleInterface::SWOOLE_MODE_BASE, $sock = SwooleInterface::SWOOLE_SOCK_TCP)
    {
        $this->server = new \swoole_server($host, $port, $mode, $sock);

        $this->manager = new ServerManager($this);
    }

    /**
     * @param $host
     * @param $port
     * @param $mode
     * @param $sock
     * @return static
     */
    public static function create($host, $port, $mode = SwooleInterface::SWOOLE_MODE_BASE, $sock = SwooleInterface::SWOOLE_SOCK_TCP)
    {
        return new static($host, $port, $mode, $sock);
    }

    /**
     * Get server pid file absolute path.
     *
     * @return string
     */
    public function getPidFile()
    {
        return str_replace('{name}', $this->getName(), $this->pid_file);
    }

    /**
     * @return int|null
     */
    public function getPid()
    {
        return (int)@file_get_contents($this->getPidFile());
    }

    /**
     * @return string
     */
    public function getLogFile()
    {
        return str_replace('{name}', $this->getName(), $this->log_file);
    }

    /**
     * Get server name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->process_name;
    }

    /**
     * @return $this
     */
    public function daemonize()
    {
        $this->daemonize = true;

        $this->config['daemonize'] = true;

        return $this;
    }

    /**
     * @param array|string $config Server config array or Server config file.
     * @return null
     */
    public function config($config)
    {
        if (is_string($config)) {
            switch(pathinfo($config, PATHINFO_EXTENSION)) {
                case 'ini':
                    $config = parse_ini_file($config);
                    break;
                case 'php':
                default:
                    $config = include $config;
            }
        }

        $this->config = array_merge($this->config, $config);

        if (isset($this->config['log_file'])) {
            $this->log_file = $this->config['log_file'];
            unset($this->config['log_file']);
        }

        if (isset($this->config['pid_file'])) {
            $this->pid_file = $this->config['pid_file'];
            unset($this->config['pid_file']);
        }
    }

    /**
     * @param HandlerInterface $handlerInterface
     * @return $this
     */
    public function handle(HandlerInterface $handlerInterface)
    {
        $this->handler = $handlerInterface->handle($this);

        return $this;
    }

    /**
     * @param      $name
     * @param      $callback
     * @return $this
     */
    public function on($name, $callback)
    {
        $this->server->on($name, $callback);

        return $this;
    }

    /**
     * @return mixed
     */
    public function start()
    {
        $this->server->set($this->config);

        if (null === $this->handler) {
            throw new \RuntimeException("Server is not has handler.");
        }

        return $this->server->start();
    }
}