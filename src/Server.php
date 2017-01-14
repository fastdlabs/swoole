<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;

use Exception;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput as Output;
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
    const VERSION = '1.0.0 (dev)';

    /**
     * @var $name
     */
    protected $name;

    /**
     * @var Output
     */
    protected $output;

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
    protected $configFile;

    /**
     * @var string
     */
    protected $host = '127.0.0.1';

    /**
     * @var string
     */
    protected $port = '9527';

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
    protected $listens = [];

    /**
     * @var Process[]
     */
    protected $processes = [];

    /**
     * Server constructor.
     *
     * @param $name
     * @param null $address
     */
    public function __construct($name, $address = null)
    {
        $this->name($name);

        if (null === $address) {
            $address = 'tcp://' . $this->host . ':' . $this->port;
        }

        $info = parse_address($address);

        $this->type = $info['sock'];
        $this->host = $info['host'];
        $this->port = $info['port'];

        $this->output = new Output();

        $this->config = array_merge($this->config, (array) $this->configure());
    }

    /**
     * Please return swoole configuration array.
     *
     * @return array
     */
    abstract public function configure();

    /**
     * @return bool
     */
    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * @param $name
     * @return $this;
     */
    public function name($name)
    {
        $this->name = $name;

        $this->pid = '/tmp/' . str_replace(' ', '-', $this->name) . '.pid';

        return $this;
    }

    /**
     * @param $pidFile
     * @return $this
     */
    public function pid($pidFile)
    {
        $this->pid = $pidFile;

        return $this;
    }

    /**
     * 守護進程, debug模式下无法开启
     *
     * @return $this
     */
    public function daemon()
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
     * @return string
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return swoole_server
     */
    public function getSwoole()
    {
        return $this->swoole;
    }

    /**
     * @return $this
     */
    protected function handleCallback()
    {
        $handles = get_class_methods($this);
        $isListenerPort = false;
        $serverClass = get_class($this->getSwoole());
        if ('Swoole\Server\Port' == $serverClass
            || 'swoole_server_port' == $serverClass) {
            $isListenerPort = true;
        }
        foreach ($handles as $value) {
            if ('on' == substr($value, 0, 2)) {
                if ($isListenerPort) {
                    if (in_array($value, ['onConnect', 'onClose', 'onReceive', 'onPacket', 'onReceive'])) {
                        $this->swoole->on(lcfirst(substr($value, 2)), [$this, $value]);
                    }
                } else {
                    $this->swoole->on(lcfirst(substr($value, 2)), [$this, $value]);
                }
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getServerType()
    {
        switch (get_class($this->swoole)) {
            case 'swoole_http_server':
            case 'Swoole\Http\Server':
                return 'http';
            case 'swoole_websocket_server':
            case 'Swoole\WebSocket\Server':
                return 'ws';
            case 'swoole_server':
            case 'swoole_server_port':
            case 'Swoole\Server':
            case 'Swoole\Server\Port':
                return ($this->swoole->type === SWOOLE_SOCK_UDP || $this->swoole->type === SWOOLE_SOCK_UDP6) ? 'udp' : 'tcp';
            default:
                return 'unknown';
        }
    }

    /**
     * 引导服务，当启动是接收到 swoole server 信息，则默认以这个swoole 服务进行引导
     *
     * @param swoole_server $swoole
     * @return $this
     */
    public function bootstrap(swoole_server $swoole = null)
    {
        if (!$this->isBooted()) {
            $this->swoole = null === $swoole ? $this->initSwoole() : $swoole;

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
        return new swoole_server($this->host, $this->port, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
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
     * @param Process $process
     * @return $this
     */
    public function process(Process $process)
    {
        $this->processes[] = $process;

        return $this;
    }

    /**
     * @param $name
     * @param $address
     * @return static
     */
    public static function createServer($name, $address)
    {
        return new static($name, $address);
    }

    /**
     * @return int
     */
    public function start()
    {
        if (check_process($this->name)) {
            $this->output->writeln(sprintf('Server <info>[%s] %s:%s</info> address already in use', $this->name, $this->host, $this->port));
        } else {
            try {
                $this->bootstrap();
                // 多端口监听
                foreach ($this->listens as $listen) {
                    $swoole = $this->swoole->listen($listen->getHost(), $listen->getPort(), $this->swoole->type);
                    $listen->bootstrap($swoole);
                }
                // 进程控制
                foreach ($this->processes as $process) {
                    $this->swoole->addProcess($process->getProcess());
                }
                $this->swoole->start();
            } catch (Exception $e) {
                $this->output->writeln("<error>{$e->getMessage()}</error>");
            }
        }

        return 0;
    }

    /**
     * @return int
     */
    public function shutdown()
    {
        if (false === ($status = check_process($this->name))) {
            $this->output->writeln(sprintf('Server <info>%s</info> is not running...', $this->name));
            return -1;
        }

        $pid = (int)@file_get_contents($this->pid);

        posix_kill($pid, SIGTERM);

        $this->output->writeln(sprintf('Server <info>%s</info> [<info>#%s</info>] is shutdown...', $this->name, $pid));

        return 0;
    }

    /**
     * @return int
     */
    public function reload()
    {
        if (false === check_process($this->name)) {
            $this->output->writeln(sprintf('Server <info>%s</info> is not running...', $this->name));
            return -1;
        }

        $pid = (int)@file_get_contents($this->getPid());

        posix_kill($pid, SIGUSR1);

        $this->output->writeln(sprintf('Server <info>%s</info> [<info>#%s</info>] is reloading...', $this->name, $pid));

        return 0;
    }

    /**
     * @return int
     */
    public function restart()
    {
        $this->shutdown();
        sleep(1);
        return $this->start();
    }

    /**
     * @return int
     */
    public function status()
    {
        if (!($status = check_process($this->name))) {
            $this->output->writeln(sprintf('Server <info>%s</info> is not running...', $this->name));
            return -1;
        }

        exec("ps axu | grep '{$this->name}' | grep -v grep", $output);

        // list all process
        $output = array_map(function ($v) {
            $status = preg_split('/\s+/', $v);
            unset($status[2], $status[3], $status[4], $status[6], $status[9]); //
            $status = array_values($status);
            $status[5] = $status[5] . ' ' . implode(' ', array_slice($status, 6));
            return array_slice($status, 0, 6);
        }, $output);

        // combine
        $headers = ['USER', 'PID', 'RSS', 'STAT', 'START', 'COMMAND'];
        foreach ($output as $key => $value) {
            $output[$key] = array_combine($headers, $value);
        }

        $this->output->writeln(sprintf("Server: <info>%s</info>", $this->name));
        $this->output->writeln(sprintf('Swoole version <info>%s</info>', SWOOLE_VERSION));
        $this->output->writeln(sprintf('Application version <info>%s</info>', Server::VERSION));
        $this->output->writeln(sprintf("PID file: <info>%s</info>, PID: <info>%s</info>", $this->pid, (int)@file_get_contents($this->pid)));
        $table = new Table($this->output);
        $table
            ->setHeaders($headers)
            ->setRows($output)
        ;

        $table->render();

        unset($table, $headers, $output);

        return 0;
    }

    /**
     * @param array $directories
     * @return void|int
     */
    public function watch(array $directories = ['.'])
    {
        $self = $this;

        if (false === ($status = check_process($this->name))) {
            $process = new swoole_process(function () use ($self) {
                $self->start();
            }, true);
            $process->start();
        }

        foreach ($directories as $directory) {
            $this->output->writeln(sprintf('Watching directory: ["<info>%s</info>"]', realpath($directory)));
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
        if (null !== $this->pid) {
            if (!is_dir($dir = dirname($this->pid))) {
                mkdir($dir, 0755, true);
            }
            file_put_contents($this->pid, $server->master_pid);
        }

        $this->output->writeln(sprintf("Server: <info>%s</info>", $this->name));
        $this->output->writeln(sprintf('Swoole version <info>%s</info>', SWOOLE_VERSION));
        $this->output->writeln(sprintf('Application version <info>%s</info>', Server::VERSION));
        $this->output->writeln(sprintf('PID file: <info>%s</info>, PID: <info>%s</info>', $this->pid, $server->master_pid));
        process_rename($this->name . ' master');

        $this->output->writeln(sprintf("Server <info>%s://%s:%s</info>", $this->getServerType(), $this->getHost(), $this->getPort()));

        foreach ($this->listens as $listen) {
            $this->output->writeln(sprintf("> Listen <info>%s://%s:%s</info>", $this->getServerType(), $listen->getHost(), $listen->getPort()));
        }

        $this->output->writeln(sprintf('Server Master[<info>#%s</info>] is started', $server->master_pid));
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

        $this->output->writeln(sprintf('Server <info>%s</info> Master[<info>#%s</info>] is shutdown ', $this->name, $server->master_pid));
    }

    /**
     * @param swoole_server $server
     *
     * @return void
     */
    public function onManagerStart(swoole_server $server)
    {
        process_rename($this->getName() . ' manager');

        $this->output->writeln(sprintf('Server Manager[<info>#%s</info>] is started', $server->manager_pid));
    }

    /**
     * @param swoole_server $server
     *
     * @return void
     */
    public function onManagerStop(swoole_server $server)
    {
        $this->output->writeln(sprintf('Server <info>%s</info> Manager[<info>#%s</info>] is shutdown.', $this->name, $server->manager_pid));
    }

    /**
     * @param swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStart(swoole_server $server, $worker_id)
    {
        process_rename($this->getName() . ' worker');

        $this->output->writeln(sprintf('Server Worker[<info>#%s</info>] is started [<info>#%s</info>]', $server->worker_pid, $worker_id));
    }

    /**
     * @param swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStop(swoole_server $server, $worker_id)
    {
        $this->output->writeln(sprintf('Server <info>%s</info> Worker[<info>#%s</info>] is shutdown', $this->name, $worker_id));
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
        $this->output->writeln(sprintf('Server <info>%s</info> Worker[<info>#%s</info>] error. Exit code: [<question>%s</question>]', $this->name, $worker_pid, $exit_code));
    }
}