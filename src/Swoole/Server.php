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

use FastD\Packet\Binary;
use FastD\Swoole\Console\Output;
use FastD\Swoole\Console\Process;

/**
 * Class Server
 *
 * @package FastD\Swoole\Server
 */
abstract class Server
{
    const SERVER_NAME = 'fds';

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
     * @var array
     */
    protected $discoveries = [];

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

            $this->swoole = $this->initSwoole();

            $this->swoole->on('start', [$this, 'onStart']);
            $this->swoole->on('shutdown', [$this, 'onShutdown']);
            $this->swoole->on('managerStart', [$this, 'onManagerStart']);
            $this->swoole->on('managerStop', [$this, 'onManagerStop']);
            $this->swoole->on('workerStart', [$this, 'onWorkerStart']);
            $this->swoole->on('workerStop', [$this, 'onWorkerStop']);
            $this->swoole->on('workerError', [$this, 'onWorkerError']);
            $this->swoole->on('receive', [$this, 'onReceive']);
            $this->swoole->on('packet', [$this, 'onPacket']);

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
                return 'ws';
            case 'swoole_server':
                return ($this->sockType === SWOOLE_SOCK_UDP || $this->sockType === SWOOLE_SOCK_UDP6) ? 'udp' : 'tcp';
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
     * 服务发现
     *
     * @param array $discoveries
     * @return $this
     */
    public function discovery(array $discoveries)
    {
        $this->discoveries = $discoveries;

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
     * @param \swoole_server $server
     * @param $worker_id
     * @param $task_id
     * @param $msg
     */
    public function report(\swoole_server $server, $worker_id, $task_id, $msg)
    {
        foreach ($this->monitors as $monitor) {
            $client = new Client($monitor['sock']);
            if ($client->connect($monitor['host'], $monitor['port'], 2)) {
                $client->send(Binary::encode([
                    'worker_id' => $worker_id,
                    'task_id' => $task_id,
                    'msg' => $msg
                ]));
            }
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
     * @return \swoole_server
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
    public function onReceive(\swoole_server $server, int $fd, int $from_id, string $data)
    {
        try {
            $response = $this->doWork(new Request($server, $fd, $data, $from_id));
            $response->send();
        } catch (\Exception $e) {
            $server->send(sprintf("Error: %s\nFile: %s \n Code: %s",
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getCode()
                )
            );
            $server->close($fd);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    abstract public function doWork(Request $request);

    /**
     * 服务器同时监听TCP/UDP端口时，收到TCP协议的数据会回调onReceive，收到UDP数据包回调onPacket
     *
     * @param \swoole_server $server
     * @param string $data
     * @param array $client_info
     */
    public function onPacket(\swoole_server $server, string $data, array $client_info)
    {
        try {
            $this->doPacket(new Request($server, null, $data, null, $client_info));
        } catch (\Exception $e) {
            $server->send(sprintf("Error: %s\nFile: %s \n Code: %s",
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getCode()
                )
            );
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    abstract public function doPacket(Request $request);

    /**
     * @param \swoole_server $server
     * @param $fd
     * @param $data
     * @return Response
     */
    public function response(\swoole_server $server, $fd, $data)
    {
        return new Response($server, $fd, $data);
    }

    /**
     * Base start handle. Storage process id.
     *
     * @param \swoole_server $server
     * @return void
     */
    public function onStart(\swoole_server $server)
    {
        if (null !== ($file = $this->getPid())) {
            if (!is_dir($dir = dirname($file))) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($file, $server->master_pid . PHP_EOL);
        }

        Process::rename(static::SERVER_NAME . ' master');

        Output::output(sprintf("Server %s://%s:%s", $this->getServerType(), $this->getHost(), $this->getPort()));
        Output::output(sprintf('Server Master[#%s] is started', $server->master_pid));
    }

    /**
     * Shutdown server process.
     *
     * @param \swoole_server $server
     * @return void
     */
    public function onShutdown(\swoole_server $server)
    {
        if (null !== ($file = $this->getPid()) && !empty(trim(file_get_contents($file)))) {
            unlink($file);
        }

        Output::output(sprintf('Server Master[#%s] is shutdown ', $server->master_pid));
    }

    /**
     * @param \swoole_server $server
     *
     * @return void
     */
    public function onManagerStart(\swoole_server $server)
    {
        Process::rename(static::SERVER_NAME . ' manager');

        Output::output(sprintf('Server Manager[#%s] is started', $server->manager_pid));
    }

    /**
     * @param \swoole_server $server
     *
     * @return void
     */
    public function onManagerStop(\swoole_server $server)
    {
        Output::output(sprintf('Server Manager[#%s] is shutdown.', $server->manager_pid));
    }

    /**
     * @param \swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStart(\swoole_server $server, int $worker_id)
    {
        Process::rename(static::SERVER_NAME . ' worker');

        Output::output(sprintf('Server Worker[#%s] is started [#%s]', $server->worker_pid, $worker_id));
    }

    /**
     * @param \swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStop(\swoole_server $server, int $worker_id)
    {
        Output::output(sprintf('Server Worker[#%s] is shutdown', $worker_id));
    }

    /**
     * @param \swoole_server $server
     * @param int $worker_id
     * @param int $worker_pid
     * @param int $exit_code
     * @return void
     */
    public function onWorkerError(\swoole_server $server, int $worker_id, int $worker_pid, int $exit_code)
    {
        Output::output(sprintf('Server Worker[#%s] error. Exit code: [%s]', $worker_pid, $exit_code));
    }
}