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

use FastD\Swoole\Context;
use FastD\Swoole\Handler\ServerHandler;
use FastD\Swoole\Handler\ServerHandlerInterface;
use FastD\Swoole\Handler\SwooleHandlerInterface;

/**
 * Class Swoole
 *
 * @package FastD\Swoole\Server
 */
class SwooleServer implements SwooleServerInterface
{
    /**
     * @var \swoole_server
     */
    protected $server;

    protected $config = [
        'pid_file'              => '/tmp/fd_swoole.pid',
        'process_name'          => 'fd_swoole', // 进程名
        'open_length_check'     => 1,
        'dispatch_mode'         => 3,
        'package_length_type'   => 'N',
        'package_length_offset' => 0,
        'package_body_offset'   => 4,
        'package_max_length'    => 1024 * 1024 * 2,
        'buffer_output_size'    => 1024 * 1024 * 3,
        'pipe_buffer_size'      => 1024 * 1024 * 32,
        'max_request'           => 0,
        'log_file'              => '/tmp/fd_server.log',
        'task_tmpdir'           => '/tmp/fd_tmp/',
        'user'                  => 'www',
        'group'                 => 'www',
    ];

    /**
     * @var SwooleHandlerInterface
     */
    protected $handler;

    /**
     * @var int
     */
    protected $pid;

    /**
     * @var bool
     */
    protected $daemonize = false;

    /**
     * SwooleServer constructor.
     *
     * @param                             $host
     * @param                             $port
     * @param ServerHandlerInterface|null $serverHandlerInterface
     * @param array                       $config
     */
    public function __construct($host, $port, ServerHandlerInterface $serverHandlerInterface = null, array $config = [])
    {
        $this->config = array_merge($this->config, $config);

        $this->server = new \swoole_server($host, $port, static::SERVER_MODE_PROCESS, static::SERVER_SOCK_TCP);

        $this->handler = null === $serverHandlerInterface ? new ServerHandler() : $serverHandlerInterface;

        $this->pid = (int)@file_get_contents($this->config['pid_file']);
    }

    /**
     * @param       $host
     * @param       $port
     * @param ServerHandlerInterface $serverHandlerInterface
     * @param array $config
     *
     * @return static
     */
    public static function create($host, $port, ServerHandlerInterface $serverHandlerInterface = null, array $config = [])
    {
        return new static($host, $port, $serverHandlerInterface, $config);
    }

    /**
     * Get server pid file absolute path.
     *
     * @return string
     */
    public function getPidFile()
    {
        return $this->config['pid_file'];
    }

    public function getLogFile()
    {
        return $this->config['log_file'];
    }

    public function getName()
    {
        return 'fd-server';
    }

    /**
     * @return int|null
     */
    public function getPid()
    {
        return $this->pid;
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
     * @return mixed
     */
    public function start()
    {
        $this->server->set($this->config);

        if (null === $this->handler) {
            throw new \RuntimeException("Server is not has handler.");
        }

        $this->handler->handle($this);

        return $this->server->start();
    }

    /**
     * @param SwooleHandlerInterface $swooleHandlerInterface
     * @return $this
     */
    public function handle(SwooleHandlerInterface $swooleHandlerInterface)
    {
        $this->handler = $swooleHandlerInterface;

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
     * @param $name
     * @return null
     */
    public function getConfig($name = null)
    {
        return array_key_exists($name, $this->config) ? $this->config[$name] : null;
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