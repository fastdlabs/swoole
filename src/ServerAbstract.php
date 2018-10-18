<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;


use Exception;
use swoole_process;
use swoole_server;
use swoole_server_port;
use swoole_websocket_server;
use swoole_http_server;
use FastD\Swoole\Support\Watcher;
use FastD\Swoole\Handlers\ServerHandlerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Server
 * @package FastD\Swoole
 */
abstract class ServerAbstract implements ServerInterface, ServerHandlerInterface
{
    const VERSION = '5.0.0';

    /**
     * @var $name
     */
    protected $name = '';

    /**
     * @var swoole_server
     */
    protected $swoole;

    /**
     * Swoole server run configuration.
     *
     * @var array
     */
    protected $config = [
        'worker_num' => 1,
        'task_tmpdir' => '/tmp',
        'open_cpu_affinity' => true,
    ];

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
    protected $pid_file = '';

    /**
     * @var int
     */
    protected $pid = 0;

    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * 多端口支持
     *
     * @var ServerAbstract[]
     */
    protected $listens = [];

    /**
     * @var Process[]
     */
    protected $processes = [];

    /**
     * @var Timer[]
     */
    protected $timers = [];

    /**
     * @var int
     */
    protected $fd;

    /**
     * @var ConsoleOutput
     */
    protected $output;

    /**
     * Server constructor.
     * @param string $address
     * @param array $config
     */
    public function __construct(string $address = null, array $config = [])
    {
        if (null !== $address) {
            $info = parse_url($address);

            $this->host = $info['host'];
            $this->port = $info['port'];
        }

        $this->configure($config);

        $this->output = new ConsoleOutput();
    }

    /**
     * @param string $name
     * @return ServerAbstract
     */
    public function rename(string $name): ServerInterface
    {
        $this->name = $name;

        return $this;
    }


    /**
     * @param array $config
     * @return ServerAbstract
     */
    public function configure(array $config): ServerInterface
    {
        $this->config = array_merge($this->config, $config);

        isset($config['name']) && $this->rename($config['name']);

        isset($this->config['pid_file']) && $this->pid_file = $this->config['pid_file'];

        if (empty($this->pidFile)) {
            $this->pid_file = '/tmp/' . str_replace(' ', '-', $this->name) . '.pid';
            $this->config['pid_file'] = $this->pid_file;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }

    /**
     * 守護進程
     *
     * @return ServerAbstract
     */
    public function daemon(): ServerInterface
    {
        $this->config['daemonize'] = true;

        return $this;
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return static::PROTOCOL;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Get client connection server's file descriptor.
     *
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * @return string
     */
    public function getSocketType(): string
    {
        switch (static::PROTOCOL) {
            case 'udp':
                $type = SWOOLE_SOCK_UDP;
                break;
            case 'unix':
                $type = SWOOLE_UNIX_STREAM;
                break;
            case 'tcp':
            default :
                $type = SWOOLE_SOCK_TCP;
        }

        return $type;
    }

    /**
     * @return string
     */
    public function getPidFile(): string
    {
        return $this->pid_file;
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return swoole_server
     */
    public function getSwoole(): swoole_server
    {
        return $this->swoole;
    }

    /**
     * @return ServerAbstract[]
     */
    public function getListeners(): array
    {
        return $this->listens;
    }

    /**
     * @param $name
     * @return ServerAbstract
     */
    public function getListener(string $name): ServerAbstract
    {
        return $this->listens[$name];
    }

    /**
     * @return ServerAbstract
     */
    protected function handleCallback(): ServerAbstract
    {
        $handles = get_class_methods($this);
        $isListenerPort = false;
        $serverClass = get_class($this->getSwoole());
        if ('Swoole\Server\Port' == $serverClass || 'swoole_server_port' == $serverClass) {
            $isListenerPort = true;
        }
        foreach ($handles as $value) {
            if ('on' == substr($value, 0, 2)) {
                if ($isListenerPort) {
                    if ('udp' === $this->getProtocol()) {
                        $callbacks = ['onPacket',];
                    } else {
                        $callbacks = ['onConnect', 'onClose', 'onReceive',];
                    }
                    if (in_array($value, $callbacks)) {
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
     * 引导服务，当启动是接收到 swoole server 信息，则默认以这个swoole 服务进行引导
     *
     * @param \swoole_server $swoole swoole server or swoole server port
     * @return ServerAbstract
     */
    public function bootstrap(?swoole_server $swoole = null): ServerAbstract
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
    public function initSwoole(): swoole_server
    {
        return new swoole_server($this->host, $this->port, SWOOLE_PROCESS, $this->getSocketType());
    }

    /**
     * @param ServerAbstract $server
     * @return ServerAbstract
     */
    public function listen(ServerAbstract $server): ServerAbstract
    {
        $this->listens[$server->getName()] = $server;

        return $this;
    }

    /**
     * @param Process $process
     * @return ServerAbstract
     */
    public function process(Process $process): ServerAbstract
    {
        $process->withServer($this);

        $this->processes[] = $process;

        return $this;
    }

    /**
     * @param Timer $timer
     * @return ServerAbstract
     */
    public function timer(Timer $timer): ServerAbstract
    {
        $timer->withServer($this);

        $this->timers[] = $timer;

        return $this;
    }

    /**
     * @param $address
     * @param $config
     * @return ServerAbstract
     */
    public static function createServer(?string $address = null, array $config = []): ServerAbstract
    {
        return new static($address, $config);
    }

    /**
     * @return int
     */
    public function start(): int
    {
        if ($this->isRunning()) {
            $this->output->writeln(sprintf('Server <info>[%s] %s:%s</info> address already in use', $this->name, $this->host, $this->port));
        } else {
            try {
                $this->bootstrap();
                if (!file_exists($dir = dirname($this->pid_file))) {
                    mkdir($dir, 0755, true);
                }
                // 多端口监听
                foreach ($this->listens as $listen) {
                    $swoole = $this->swoole->listen($listen->getHost(), $listen->getPort(), $listen->getSocketType());
                    $listen->bootstrap($swoole);
                }
                // 进程控制
                foreach ($this->processes as $process) {
                    $this->swoole->addProcess($process->getProcess());
                }

                $this->output->writeln(sprintf("Server: <info>%s</info>", $this->name));
                $this->output->writeln(sprintf('App version: <info>%s</info>', ServerAbstract::VERSION));
                $this->output->writeln(sprintf('Swoole version: <info>%s</info>', SWOOLE_VERSION));

                $this->swoole->start();
            } catch (Exception $e) {
                $this->output->write("<error>{$e->getMessage()}</error>\n");
            }
        }

        return 0;
    }

    /**
     * @return int
     */
    public function stop(): int
    {
        if (!$this->isRunning()) {
            $this->output->writeln(sprintf('Server <info>%s</info> is not running...', $this->name));
            return -1;
        }

        $pid = (int) @file_get_contents($this->getPidFile());
        if (process_kill($pid, SIGTERM)) {
            unlink($this->pid_file);
        }

        $this->output->writeln(sprintf('Server <info>%s</info> [<info>#%s</info>] is shutdown...', $this->name, $pid));
        $this->output->writeln(sprintf('PID file %s is unlink', $this->pid_file), OutputInterface::VERBOSITY_DEBUG);

        return 0;
    }

    /**
     * @return int
     */
    public function reload(): int
    {
        if (!$this->isRunning()) {
            $this->output->writeln(sprintf('Server <info>%s</info> is not running...', $this->name));
            return -1;
        }

        $pid = (int)@file_get_contents($this->getPidFile());

        posix_kill($pid, SIGUSR1);

        $this->output->writeln(sprintf('Server <info>%s</info> [<info>%s</info>] is reloading...', $this->name, $pid));

        return 0;
    }

    /**
     * @return int
     */
    public function restart(): int
    {
        $this->stop();

        return $this->start();
    }

    /**
     * @return array
     */
    public function status(): array
    {
        if (!$this->isRunning()) {
            $this->output->writeln(sprintf('Server <info>%s</info> is not running...', $this->name));
            return [];
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

        $table = new Table($this->output);
        $table
            ->setHeaders($headers)
            ->setRows($output)
        ;

        $this->output->writeln(sprintf("Server: <info>%s</info>", $this->name));
        $this->output->writeln(sprintf('App version: <info>%s</info>', Server::VERSION));
        $this->output->writeln(sprintf('Swoole version: <info>%s</info>', SWOOLE_VERSION));
        $this->output->writeln(sprintf("PID file: <info>%s</info>, PID: <info>%s</info>", $this->pid_file, (int) @file_get_contents($this->pid_file)) . PHP_EOL);
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
        $that = $this;

        if (!$this->isRunning()) {
            $process = new Process('server watch process', function () use ($that) {
                $that->start();
            }, true);
            $process->start();
        }

        foreach ($directories as $directory) {
            $this->output->writeln(sprintf('Watching directory: ["<info>%s</info>"]', realpath($directory)));
        }

        $watcher = new Watcher($this->output);

        $watcher->watch($directories, function () use ($that) {
            $that->reload();
        });

        $watcher->run();

        process_wait();
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        if (file_exists($this->config['pid_file'])) {
            return posix_kill(file_get_contents($this->config['pid_file']), 0);
        }

        if ($is_running = process_is_running("{$this->name} master")) {
            $is_running = port_is_running($this->port);
        }

        return $is_running;
    }

    /**
     * Base start handle. Storage process id.
     *
     * @param swoole_server $server
     * @return void
     */
    public function onStart(swoole_server $server): void
    {
        if (version_compare(SWOOLE_VERSION, '1.9.5', '<')) {
            file_put_contents($this->pid_file, $server->master_pid);
            $this->pid = $server->master_pid;
        }

        process_rename($this->name . ' master');

        $this->output->writeln(sprintf("Listen: <info>%s://%s:%s</info>", $this->getProtocol(), $this->getHost(), $this->getPort()));
        foreach ($this->listens as $listen) {
            $this->output->writeln(sprintf(" <info> ></info> Listen: <info>%s://%s:%s</info>", $listen->getProtocol(), $listen->getHost(), $listen->getPort()));
        }

        $this->output->writeln(sprintf('PID file: <info>%s</info>, PID: <info>%s</info>', $this->pid_file, $server->master_pid));
        $this->output->writeln(sprintf('Server Master[<info>%s</info>] is started', $server->master_pid), OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * Shutdown server process.
     *
     * @param swoole_server $server
     * @return void
     */
    public function onShutdown(swoole_server $server): void
    {
        if (file_exists($this->pid_file)) {
            unlink($this->pid_file);
        }

        $this->output->writeln(sprintf('Server <info>%s</info> Master[<info>%s</info>] is shutdown ', $this->name, $server->master_pid), OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * @param swoole_server $server
     *
     * @return void
     */
    public function onManagerStart(swoole_server $server): void
    {
        process_rename($this->getName() . ' manager');

        $this->output->writeln(sprintf('Server Manager[<info>%s</info>] is started', $server->manager_pid), OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * @param swoole_server $server
     *
     * @return void
     */
    public function onManagerStop(swoole_server $server): void
    {
        $this->output->writeln(sprintf('Server <info>%s</info> Manager[<info>%s</info>] is shutdown.', $this->name, $server->manager_pid), OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * @param swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStart(swoole_server $server, int $worker_id): void
    {
        $worker_name = $server->taskworker ? 'task' : 'worker';
        process_rename($this->getName() . ' ' . $worker_name);
        $this->output->write(sprintf('Server %s[<info>%s</info>] is started [<info>%s</info>]', ucfirst($worker_name), $server->worker_pid, $worker_id) . PHP_EOL);
    }

    /**
     * @param swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStop(swoole_server $server, int $worker_id): void
    {
        $this->output->writeln(sprintf('Server <info>%s</info> Worker[<info>%s</info>] is shutdown', $this->name, $worker_id), OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * @param swoole_server $server
     * @param $workerId
     * @param $workerPid
     * @param $code
     */
    public function onWorkerError(swoole_server $server, int $workerId, int $workerPid, int $code): void
    {
        $this->output->writeln(sprintf('Server <info>%s:%s</info> Worker[<info>%s</info>] error. Exit code: [<question>%s</question>]', $this->name, $workerPid, $workerId, $code), OutputInterface::VERBOSITY_DEBUG);
    }
}