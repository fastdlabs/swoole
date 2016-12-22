<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;

use FastD\Swoole\Support\Watcher;
use swoole_process;
use swoole_server;
use swoole_server_port;

/**
 * Class Server
 * @package FastD\Swoole
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
    protected $confFile;

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
     * @var bool
     */
    protected $debug = false;

    /**
     * 多端口支持
     *
     * @var Server[]
     */
    protected $listens = [];

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
    public function __construct($address, $config = null, $mode = SWOOLE_PROCESS)
    {
        $info = parse_address($address);

        $this->sockType = $info['sock'];
        $this->host = $info['host'];
        $this->port = $info['port'];
        $this->mode = $mode;

        $this->configure($config);
    }

    /**
     * @return $this
     */
    public function enableDebug()
    {
        $this->debug = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @return bool
     */
    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * 守護進程
     *
     * @return $this
     */
    public function daemonize()
    {
        $this->config['daemonize'] = true;

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
     * @return int
     */
    public function getSockType()
    {
        return $this->sockType;
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
     * @return swoole_server
     */
    public function getSwoole()
    {
        return $this->swoole;
    }

    /**
     * Bootstrap server.
     *
     * @param swoole_server_port $swoole
     * @return $this
     */
    public function bootstrap(swoole_server_port $swoole = null)
    {
        if (!$this->isBooted()) {

            $this->swoole = null === $swoole ? $this->initSwoole() : $swoole;

            $this->swoole->set($this->config);

            handle($this);

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
        return new \Swoole\Server($this->host, $this->port, $this->mode, $this->sockType);
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configure($config)
    {
        if (is_string($config) && file_exists($config)) {
            $this->confFile = $config;
            $config = include $config;
        } else if (!is_array($config)) {
            $config = [];
        }

        if (isset($config['pid'])) {
            if ('/' === $config['pid']{0}) {
                $this->pid = $config['pid'];
            } else if ('.' === $config['pid']{0}) {
                $this->pid = realpath('.') . substr($config['pid'], 1);
            }
            unset($config['pid']);
        } else {
            $this->pid = realpath('.') . '/run/' . static::SERVER_NAME . '.pid';
        }

        $this->config = $config;

        return $this;
    }

    /**
     * @param Server $server
     * @return $this
     */
    public function listen(Server $server)
    {
        $this->listens[] = $server;

        return $this;
    }

    /**
     * @param null $address
     * @param array $config
     * @param $mode
     * @return Server
     */
    public static function getInstance($address = null, array $config = [], $mode = SWOOLE_PROCESS)
    {
        if (null === static::$instance) {
            static::$instance = new static($address, $config, $mode);
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
            output(sprintf('<red>%s:%s</red> address already in use', $this->getHost(), $this->getPort()));
        } else {
            try {
                $this->bootstrap();
                $server = $this->getSwoole();
                foreach ($this->listens as $listen) {
                    $swoole = $server->listen($listen->getHost(), $listen->getPort(), $listen->getSockType());
                    $listen->bootstrap($swoole);
                }
                $server->start();
            } catch (\Exception $e) {
                output($e->getMessage());
            }
        }
    }

    /**
     * @return int
     */
    public function shutdown()
    {
        if (false === ($status = $this->isRunning())) {
            output(sprintf('Server is not running...'));
            return -1;
        }

        $pid = (int)@file_get_contents($this->getPid());

        posix_kill($pid, SIGTERM);

        output(sprintf('Server [#<info>%s</info>] is shutdown...', $pid));

        return 0;
    }

    /**
     * @return int
     */
    public function reload()
    {
        if (false === ($status = $this->isRunning())) {
            output(sprintf('Server is not running...'));
            return -1;
        }

        $pid = (int)@file_get_contents($this->getPid());

        posix_kill($pid, SIGUSR1);

        output(sprintf('Server [#<info>%s</info>] is reloading...', $pid));

        return 0;
    }

    /**
     * @return int
     */
    public function status()
    {
        if (!($status = $this->isRunning())) {
            output(sprintf('Server is not running...'));
            return -1;
        }

        $keys = array_map(function ($v) {
            return strtoupper($v);
        }, array_keys($status[0]));

        output_table($keys, $status);

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
            output(sprintf('Watching directory: ["<info>%s</info>"]', realpath($directory)));
        }

        $watcher = new Watcher();

        $watcher->watch($directories, function () use ($self) {
            $self->reload();
        });

        $watcher->run();

        swoole_process::wait();
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

        process_rename(static::SERVER_NAME . ' master' . (empty($this->confFile) ? '' : ('(' . $this->confFile . ')')));

        output(sprintf("Server <green>%s://%s:%s</green>", server_type($this->getSwoole()), $this->getHost(), $this->getPort()));
        foreach ($this->listens as $listen) {
            output(sprintf("> Listen <green>%s://%s:%s</green>", server_type($listen->getSwoole()), $listen->getHost(), $listen->getPort()));
        }
        output(sprintf('Server Master[<blue>#%s</blue>] is started', $server->master_pid));
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

        output(sprintf('Server Master[<blue>#%s</blue>] is shutdown ', $server->master_pid));
    }

    /**
     * @param swoole_server $server
     *
     * @return void
     */
    public function onManagerStart(swoole_server $server)
    {
        process_rename(static::SERVER_NAME . ' manager');

        output(sprintf('Server Manager[<blue>#%s</blue>] is started', $server->manager_pid));
    }

    /**
     * @param swoole_server $server
     *
     * @return void
     */
    public function onManagerStop(swoole_server $server)
    {
        output(sprintf('Server Manager[<blue>#%s</blue>] is shutdown.', $server->manager_pid));
    }

    /**
     * @param swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStart(swoole_server $server, $worker_id)
    {
        process_rename(static::SERVER_NAME . ' worker');

        output(sprintf('Server Worker[<blue>#%s</blue>] is started [<blue>#%s</blue>]', $server->worker_pid, $worker_id));
    }

    /**
     * @param swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStop(swoole_server $server, $worker_id)
    {
        output(sprintf('Server Worker[<blue>#%s</blue>] is shutdown', $worker_id));
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
        output(sprintf('Server Worker[<red>#%s</red>] error. Exit code: [<error>%s</error>]', $worker_pid, $exit_code));
    }
}