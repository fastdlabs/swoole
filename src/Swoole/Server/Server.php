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

use FastD\Packet\Binary;
use FastD\Swoole\Client\Client;

/**
 * Class Server
 *
 * @package FastD\Swoole\Server
 */
abstract class Server extends ServerCallbackHandle implements ServerInterface,
    ServerDiscoveryInterface,
    ServerMonitorInterface
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
     * @var array
     */
    protected $ports = [];

    /**
     * @var array
     */
    protected $monitors = [];

    /**
     * @var Server
     */
    protected static $instance;

    /**
     * Server constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->pid = realpath('.') . '/run/' . static::SERVER_NAME . '.pid';

        $this->configure($config);
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
            $self = $this;

            $this->swoole = $this->initSwoole();

            $this->scan($this->swoole);

            $this->swoole->on('receive', [$this, 'onReceive']);

            $this->swoole->on('task', [$this, 'onTask']);

            foreach ($this->ports as $key => $port) {
                $serverPort = $this->swoole->listen($port['host'], $port['port'], $port['sock']);
                if (isset($port['config'])) {
                    $serverPort->set($port['config']);
                }
                $this->ports[$key] = $serverPort;
            }

            if (isset($this->config['discoveries']) && !empty($this->config['discoveries'])) {
                $this->discovery($this->config['discoveries']);
                unset($this->config['discoveries']);
            }

            if (isset($this->config['monitors']) && !empty($this->config['monitors'])) {
                $this->monitoring($this->config['monitors']);
                unset($this->config['monitors']);
            }

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
        return new \swoole_server($this->host, $this->port, $this->mode, $this->sockType);
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

        if (isset($config['ports'])) {
            $this->ports = $config['ports'];
            unset($config['ports']);
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
            default:
                return 'unknow';
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
     * 服务发现
     *
     * @param array $discoveries
     * @return $this
     */
    public function discovery(array $discoveries)
    {
        foreach ($discoveries as $discovery) {
            $process = new \swoole_process(function () use ($discovery) {
                while (true) {
                    sleep(1);
                    echo 'discovery ' . $discovery['host'] . PHP_EOL;
                }
            });

            $this->swoole->addProcess($process);
        }

        return $this;
    }

    /**
     * @param array $monitors
     * @return $this
     */
    public function monitoring(array $monitors)
    {
        $this->monitors = $monitors;

        $self = $this;

        foreach ($this->monitors as $monitor) {
            $process = new \swoole_process(function () use ($monitor, $self) {
                $client = new Client($monitor['sock']);
                while (true) {
                    $client->connect($monitor['host'], $monitor['port']);
                    $client->send(Binary::encode([
                        'host' => $self->getHost(),
                        'port' => $self->getPort(),
                        'status' => $self->getSwooleInstance()->stats(),
                    ]));
                    sleep(20);
                }
            });

            $this->swoole->addProcess($process);
        }

        return $this;
    }

    /**
     * @param callable $callable
     * @return void
     */
    public function report(callable $callable)
    {
        try {
            $start = microtime(true);
            $callable();
            $end = microtime(true);
            $tc = $end - $start;
            $message = [
                'code' => 0,
                'content' => [
                    'start' => $start,
                    'end' => $end,
                    'tc' => $tc,
                ]
            ];
        } catch (\Exception $e) {
            $message = [
                'error' => $e->getCode(),
                'content' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            ];
        }

        foreach ($this->monitors as $monitor) {
            $client = new Client($monitor['sock']);
            $client->connect($monitor['host'], $monitor['port']);
            $client->send(Binary::encode($message));
            unset($client);
        }
    }

    /**
     * @param array $config
     * @return Server
     */
    public static function getInstance(array $config = [])
    {
        if (null === static::$instance) {
            static::$instance = new static($config);
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
     * @param array $config
     * @return void
     */
    public static function run(array $config)
    {
        $server = static::getInstance($config);

        $server->start();
    }

    /**
     * @return void
     */
    public function start()
    {
        $this->bootstrap();

        $this->swoole->set($this->config);

        $this->swoole->start();
    }

    /**
     * @return void
     */
    public function status()
    {
        $this->bootstrap();

        $this->swoole->stats();
    }

    /**
     * @return void
     */
    public function reload()
    {
        $this->bootstrap();

        $this->swoole->reload();
    }

    /**
     * @return void
     */
    public function shutdown()
    {
        $this->bootstrap();

        $this->swoole->shutdown();
    }

    /**
     * 服务器同时监听TCP/UDP端口时，收到TCP协议的数据会回调onReceive，收到UDP数据包回调onPacket
     *
     * @param \swoole_server $server
     * @param $fd
     * @param $from_id
     * @param $data
     * @return mixed
     */
    public function onReceive(\swoole_server $server, $fd, $from_id, $data)
    {
        $self = $this;

        $this->report(function () use ($self, $server, $fd, $from_id, $data) {
            $self->doWork($server, $fd, $from_id, $data);
        });
    }

    /**
     * @param \swoole_server $server
     * @param int $fd
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    abstract public function doWork(\swoole_server $server, int $fd, int $from_id, string $data);

    /**
     * 服务器同时监听TCP/UDP端口时，收到TCP协议的数据会回调onReceive，收到UDP数据包回调onPacket
     *
     * @param \swoole_server $server
     * @param string $data
     * @param array $client_info
     */
    public function onPacket(\swoole_server $server, string $data, array $client_info)
    {
        $self = $this;

        $this->report(function () use ($self, $server, $data, $client_info) {
            $self->doPacket($server, $data, $client_info);
        });
    }

    /**
     * @param \swoole_server $server
     * @param string $data
     * @param array $client_info
     */
    public function doPacket(\swoole_server $server, string $data, array $client_info)
    {
        return;
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
     * @param \swoole_server $server
     * @param int $task_id
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    public function doTask(\swoole_server $server, int $task_id, int $from_id, string $data)
    {
        return;
    }

    /**
     * @param \swoole_server $server
     * @param int $task_id
     * @param string $data
     * @return mixed
     */
    public function onFinish(\swoole_server $server, int $task_id, string $data)
    {
        return $this->doFinish($server, $task_id, $data);
    }

    /**
     * @param \swoole_server $server
     * @param int $task_id
     * @param string $data
     * @return mixed
     */
    public function doFinish(\swoole_server $server, int $task_id, string $data)
    {
        return;
    }
}