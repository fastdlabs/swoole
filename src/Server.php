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
use FastD\Swoole\Watch\Watcher;
use swoole_process;
use swoole_server;

/**
 * Class Server
 *
 * @package FastD\Swoole\Server
 */
abstract class Server
{
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
     * @param null $address
     * @param array $config
     * @param $mode
     */
    public function __construct($address = null, array $config = [], $mode = SWOOLE_PROCESS)
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
     * @param null $address
     * @param array $config
     * @param $mode
     */
    public static function run($address = null, array $config = [], $mode = SWOOLE_PROCESS)
    {
        $server = static::getInstance($address, $config, $mode);

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

    /**
     * Base start handle. Storage process id.
     *
     * @param swoole_server $server
     * @return void
     */
    public function onStart(swoole_server $server)
    {
        if (null !== ($file = $this->getPid())) {
            if (!is_dir($dir = dirname($file))) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($file, $server->master_pid . PHP_EOL);
        }

        $this->rename(static::SERVER_NAME . ' master');

        $this->output(sprintf("Server %s://%s:%s", $this->getServerType(), $this->getHost(), $this->getPort()));
        foreach ($this->ports as $port) {
            $this->output(sprintf("> Listen %s://%s:%s", $this->getServerType($port->type), $port->host, $port->port));
        }
        $this->output(sprintf('Server Master[#%s] is started', $server->master_pid));
    }

    /**
     * Shutdown server process.
     *
     * @param swoole_server $server
     * @return void
     */
    public function onShutdown(swoole_server $server)
    {
        if (null !== ($file = $this->getPid()) && !empty(trim(file_get_contents($file)))) {
            unlink($file);
        }

        $this->output(sprintf('Server Master[#%s] is shutdown ', $server->master_pid));
    }

    /**
     * @param swoole_server $server
     *
     * @return void
     */
    public function onManagerStart(swoole_server $server)
    {
        $this->rename(static::SERVER_NAME . ' manager');

        $this->output(sprintf('Server Manager[#%s] is started', $server->manager_pid));
    }

    /**
     * @param swoole_server $server
     *
     * @return void
     */
    public function onManagerStop(swoole_server $server)
    {
        $this->output(sprintf('Server Manager[#%s] is shutdown.', $server->manager_pid));
    }

    /**
     * @param swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStart(swoole_server $server, $worker_id)
    {
        $this->rename(static::SERVER_NAME . ' worker');

        $this->output(sprintf('Server Worker[#%s] is started [#%s]', $server->worker_pid, $worker_id));
    }

    /**
     * @param swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStop(swoole_server $server, $worker_id)
    {
        $this->output(sprintf('Server Worker[#%s] is shutdown', $worker_id));
    }

    /**
     * @param swoole_server $server
     * @param int $worker_id
     * @param int $worker_pid
     * @param int $exit_code
     * @return void
     */
    public function onWorkerError(swoole_server $server, $worker_id, $worker_pid, $exit_code)
    {
        $this->output(sprintf('Server Worker[#%s] error. Exit code: [%s]', $worker_pid, $exit_code));
    }

    /**
     * @return bool
     */
    protected function isRunning()
    {
        $processName = $this->getServerName();

        if ('Linux' !== PHP_OS) {
            $processName = $_SERVER['SCRIPT_NAME'];
        }
        // | awk '{print $1, $2, $6, $8, $9, $11, $12}'
        exec("ps axu | grep '{$processName}' | grep -v grep", $output);

        if (empty($output)) {
            return false;
        }

        $output = array_map(function ($v) {
            $status = preg_split('/\s+/', $v);

            unset($status[2], $status[3], $status[4], $status[6], $status[9]); //

            $status = array_values($status);

            $status[5] = $status[5] . ' ' . implode(' ', array_slice($status, 6));

            return array_slice($status, 0, 6);
        }, $output);

        $keys = ['user', 'pid', 'rss', 'stat', 'start', 'command'];

        foreach ($output as $key => $value) {
            $output[$key] = array_combine($keys, $value);
        }

        unset($keys);

        return $output;
    }

    /**
     * @return void
     */
    public function start()
    {
        if ($this->isRunning()) {
            $this->output(sprintf('%s:%s address already in use', $this->getHost(), $this->getPort()));
        } else {
            try {
                $this->bootstrap();
                $this->getSwoole()->start();
            } catch (\Exception $e) {
                $this->output($e->getMessage());
            }
        }
    }

    /**
     * @return int
     */
    public function shutdown()
    {
        if (false === ($status = $this->isRunning())) {
            $this->output(sprintf('Server is not running...'));
            return -1;
        }

        $pid = (int) @file_get_contents($this->getPid());

        posix_kill($pid, SIGTERM);

        $this->output(sprintf('Server [#%s] is shutdown...', $pid));

        return 0;
    }

    /**
     * @return int
     */
    public function reload()
    {
        if (false === ($status = $this->isRunning())) {
            $this->output(sprintf('Server is not running...'));
            return -1;
        }

        $pid = (int) @file_get_contents($this->getPid());

        posix_kill($pid, SIGUSR1);

        $this->output(sprintf('Server [#%s] is reloading...', $pid));

        return 0;
    }

    /**
     * @return int
     */
    public function status()
    {
        if (!($status = $this->isRunning())) {
            $this->output(sprintf('Server is not running...'));
            return -1;
        }

        $keys = array_map(function ($v) {
            return strtoupper($v);
        }, array_keys($status[0]));

        $length = 20;

        $format = function ($v) use ($length) {
            $l = floor($length - strlen($v)) / 2;
            return str_repeat(' ', $l) . $v . str_repeat(' ', (strlen($v) % 2 == 1 ? ($l) : $l + 1));
        };

        echo '|' . implode('|', array_fill(0, count($keys), str_repeat('-', $length))) . '|' . PHP_EOL;

        echo '|' . implode('|', array_map($format, $keys)) . '|' . PHP_EOL;

        echo '|' . implode('|', array_fill(0, count($keys), str_repeat('-', $length))) . '|' . PHP_EOL;
        foreach ($status as $stats) {
            echo '|' . implode('|', array_map($format, array_values($stats))) . '|' . PHP_EOL;
        }

        echo '|' . implode('|', array_fill(0, count($keys), str_repeat('-', $length))) . '|' . PHP_EOL;

        return 0;
    }

    /**
     * @param array $directories
     * @return void|int
     */
    public function watch(array $directories = ['.'])
    {
        $self = $this;

        if (false === ($status = $this->isRunning())) {
            $process = new swoole_process(function () use ($self) {
                $self->start();
            }, true);
            $process->start();
        }

        foreach ($directories as $directory) {
            $this->output(sprintf('Watching directory: ["%s"]', realpath($directory)));
        }

        $watcher = new Watcher();

        $watcher->watch($directories, function () use ($self) {
            $self->reload();
        });

        $watcher->run();

        swoole_process::wait();
    }

    /**
     * @param $msg
     * @return void
     */
    public function output($msg)
    {
        echo $this->format($msg);
    }

    /**
     * @param $msg
     * @return string
     */
    public function format($msg)
    {
        return sprintf("[%s]\t" . $msg . PHP_EOL, date('Y-m-d H:i:s'));
    }

    /**
     * @param $name
     */
    public function rename($name)
    {
        // hidden Mac OS error。
        set_error_handler(function () {});

        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($name);
        } else if (function_exists('swoole_set_process_name')) {
            swoole_set_process_name($name);
        }

        restore_error_handler();
    }

    /**
     * @param $address
     * @return array
     */
    public function parse($address)
    {
        if (false === ($info = parse_url($address))) {
            throw new AddressIllegalException($address);
        }

        switch (strtolower($info['scheme'])) {
            case 'tcp':
            case 'unix':
                $sock = SWOOLE_SOCK_TCP;
                break;
            case 'udp':
                $sock = SWOOLE_SOCK_UDP;
                break;
            case 'http':
            case 'ws':
                $sock = null;
                break;
            default:
                throw new CantSupportSchemeException($info['scheme']);
        }

        return array_merge($info, [
            'sock' => $sock
        ]);
    }
}