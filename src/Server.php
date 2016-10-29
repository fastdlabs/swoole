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

namespace FastD\Swoole;

use FastD\Swoole\Exceptions\AddressIllegalException;
use FastD\Swoole\Exceptions\CantSupportSchemeException;
use FastD\Swoole\Tools\Console;
use FastD\Swoole\Tools\PresetCallback;
use FastD\Swoole\Tools\Scheme;
use swoole_process;
use swoole_server;

/**
 * Class Server
 *
 * @package FastD\Swoole\Server
 */
abstract class Server
{
    use PresetCallback, Console, Scheme;

    const SERVER_NAME = 'fds';

    /**
     * @var swoole_server
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
    protected $host = '127.0.0.1';

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
     * @var bool
     */
    protected $booted = false;

    /**
     * 多端口支持
     *
     * @var Server[]
     */
    protected $ports = [];

    /**
     * @var Server
     */
    protected static $instance;

    /**
     * Server constructor.
     *
     * @param $address
     * @param $mode
     * @param array $config
     */
    public function __construct($address = null, $mode = SWOOLE_PROCESS, array $config = [])
    {
        if (null !== $address) {
            $info = $this->parse($address);

            $this->sockType = $info['sock'];
            $this->host = $info['host'];
            $this->port = $info['port'];
            $this->mode = $mode;
        }

        $this->pid = realpath('.') . '/run/' . static::SERVER_NAME . '.pid';

        $this->configure($config);
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return isset($this->config['debug']) ? $this->config['debug'] : false;
    }

    /**
     * @return bool
     */
    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * Bootstrap server.
     *
     * @return $this
     */
    public function bootstrap()
    {
        if (!$this->isBooted()) {

            $this->swoole = $this->initSwoole();

            $this->swoole->set($this->config);

            $this->handleCallback();

            $this->booted = true;
        }

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
     * @return $this
     */
    public function configure(array $config)
    {
        if (isset($config['pid'])) {
            $this->pid = $config['pid'];
            unset($config['pid']);
        }

        $this->config = $config;

        return $this;
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
     * @param $sock
     * @return string
     */
    public function getServerType($sock = null)
    {
        if (null === $sock) {
            $sock = $this->sockType;
        }

        switch (get_class($this->swoole)) {
            case 'swoole_http_server':
                return 'http';
            case 'swoole_websocket_server':
                return 'ws';
            case 'swoole_server':
                return ($sock === SWOOLE_SOCK_UDP || $sock === SWOOLE_SOCK_UDP6) ? 'udp' : 'tcp';
            default:
                return 'unknown';
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
     * @return string
     */
    public function getServerName()
    {
        return static::SERVER_NAME;
    }

    /**
     * @param $address
     * @param $mode
     * @param array $config
     * @return Server
     */
    public static function getInstance($address = null, $mode = SWOOLE_PROCESS, array $config = [])
    {
        if (null === static::$instance) {
            static::$instance = new static($address, $mode, $config);
        }

        return static::$instance;
    }

    /**
     * @return swoole_server
     */
    public function getSwooleInstance()
    {
        return $this->swoole;
    }

    /**
     * @param $address
     * @param $model
     * @param array $config
     * @return void
     */
    public static function run($address = null, $model = SWOOLE_PROCESS, array $config = [])
    {
        $server = static::getInstance($address, $model, $config);

        $server->start();
    }

    /**
     * @return $this
     */
    public function handleCallback()
    {
        $handles = get_class_methods($this);

        foreach ($handles as $value) {
            if ('on' == substr($value, 0, 2)) {
                $this->swoole->on(lcfirst(substr($value, 2)), [$this, $value]);
            }
        }
    }

    /**
     * @return swoole_server
     */
    public function getSwoole()
    {
        return $this->swoole;
    }
}