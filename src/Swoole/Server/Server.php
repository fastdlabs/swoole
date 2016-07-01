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

use swoole_server;

/**
 * Class Server
 *
 * @package FastD\Swoole\Server
 */
abstract class Server extends ServerCallbackHandle implements ServerInterface
{
    /**
     * @var \swoole_server
     */
    protected $swoole;

    /**
     * Swoole server run configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $host = '::1';

    /**
     * @var string
     */
    protected $port = '9527';

    /**
     * @var int
     */
    protected $mode = SWOOLE_PROCESS;

    /**
     * @var int
     */
    protected $sockType = SWOOLE_SOCK_TCP;

    /**
     * @var string
     */
    protected $pid;

    /**
     * @var Server
     */
    protected static $instance;

    /**
     * Server constructor.
     */
    public function __construct()
    {
        $this->pid = realpath('.') . '/run/' . static::SERVER_NAME . '.pid';
    }

    /**
     * Bootstrap server.
     *
     * @return $this
     */
    public function bootstrap()
    {
        $this->swoole = $this->initSwoole();

        $self = static::getInstance();

        $this->scan($this->swoole);

        $this->swoole->on('receive', function (\swoole_server $server, int $fd, int $from_id, string $data) use ($self) {
            $self->doWork($server, $fd, $from_id, $data);
        });

        $this->swoole->on('task', function (\swoole_server $server, int $task_id, int $from_id, string $data) use ($self) {
            return $self->doTask($server, $task_id, $from_id, $data);
        });

        return $this;
    }

    /**
     * 如果需要自定义自己的swoole服务器,重写此方法
     *
     * @return swoole_server
     */
    public function initSwoole()
    {
        return new swoole_server($this->host, $this->port, $this->mode, $this->sockType);
    }

    /**
     * @param array $config
     * @return array
     */
    public function configure(array $config)
    {
        if (isset($config['host'])) {
            $this->host = $config['host'];
            unset($config['host']);
        }
        if (isset($config['port'])) {
            $this->port = $config['port'];
            unset($config['port']);
        }
        if (isset($config['mode'])) {
            $this->mode = $config['mode'];
            unset($config['mode']);
        }
        if (isset($config['sock'])) {
            $this->sockType = $config['sock'];
            unset($config['sock']);
        }
        if (isset($config['pid'])) {
            $this->pid = $config['pid'];
            unset($config['pid']);
        }

        $this->config = $config;

        return $config;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getServerType()
    {
        switch (get_class($this->swoole)) {
            case 'swoole_http_server':
                return 'http';
            case 'swoole_websocket_server':
                return '';
            case 'swoole_server':
                return ($this->sockType === SWOOLE_SOCK_UDP || $this->sockType === SWOOLE_SOCK_UDP6) ? 'udp' : 'tcp';
        }
    }

    /**
     * @return string
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return Server
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param array $config
     * @return void
     */
    public static function run(array $config)
    {
        $server = static::getInstance();

        $server->configure($config);

        $server->bootstrap();

        $server->start();
    }

    /**
     * @return string
     */
    public function getServerName()
    {
        return static::SERVER_NAME;
    }

    /**
     * @return void
     */
    public function start()
    {
        $this->swoole->set($this->config);

        $this->swoole->start();
    }

    /**
     * @return void
     */
    public function status()
    {
        $this->swoole->stats();
    }

    /**
     * @return void
     */
    public function reload()
    {
        $this->swoole->reload();
    }

    /**
     * @return void
     */
    public function shutdown()
    {
        $this->swoole->shutdown();
    }

    /**
     * @return swoole_server
     */
    public function getSwooleInstance()
    {
        return $this->swoole;
    }

    /**
     * @param \swoole_server $server
     * @param $fd
     * @param $from_id
     * @param $data
     * @return mixed
     */
    public function onReceive(\swoole_server $server, $fd, $from_id, $data)
    {
        $this->doWork($server, $fd, $from_id, $data);
    }

    /**
     * @param \swoole_server $server
     * @param int $task_id
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    public function onTask(\swoole_server $server, int $task_id, int $from_id, string $data)
    {
        return $this->doTask($server, $task_id, $from_id, $data);
    }

    /**
     * @param swoole_server $server
     * @param int $task_id
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    public function doTask(\swoole_server $server, int $task_id, int $from_id, string $data)
    {
        return;
    }
}