<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;


use FastD\Swoole\Handlers\HandlerInterface;
use FastD\Swoole\Support\Watcher;
use FastD\Swoole\Traits\Manger;
use swoole_process;
use Swoole\Server;
use Server\Port;
use FastD\Swoole\Handlers\ServerHandlerInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Server
 * @package FastD\Swoole
 */
abstract class ServerAbstract
{
    const VERSION = '5.0';

    protected string $protocol = 'tcp';

    /**
     * @var string $name
     */
    protected string $name = '';

    /**
     * @var Server
     */
    protected Server $swoole;

    /**
     * @var array
     */
    protected $config = [
        'worker_num' => 1,
        'task_tmpdir' => '/tmp',
        'open_cpu_affinity' => true,
        'pid_file' => '/tmp/swoole.pid'
    ];

    /**
     * @var string
     */
    public string $host = '127.0.0.1';

    /**
     * @var int
     */
    public int $port = 9527;

    /**
     * @var int
     */
    protected int $sock_type = SWOOLE_SOCK_TCP;

    /**
     * @var int
     */
    protected int $mode = SWOOLE_PROCESS;

    /**
     * @var string
     */
    protected string $pid_file = '/tmp/swoole.pid';

    /**
     * @var bool
     */
    protected bool $booted = false;

    /**
     * @var HandlerInterface
     */
    protected HandlerInterface $handler;

    /**
     * 多端口支持
     *
     * @var ServerAbstract[]
     */
    protected array $listens = [];

    /**
     * @var Process[]
     */
    protected array $processes = [];

    /**
     * ServerAbstract constructor.
     * @param string $host
     * @param int $port
     * @param int $mode
     * @param int $sock_type
     */
    public function __construct(string $host = '127.0.0.1', int $port = 9527, int $mode = SWOOLE_PROCESS, int $sock_type = SWOOLE_SOCK_TCP)
    {
        $this->host = $host;
        $this->port = $port;
        $this->mode = $mode;
        $this->sock_type = $sock_type;
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
     * @return int
     */
    public function getSocketType(): int
    {
        return $this->sock_type;
    }

    /**
     * @return Server
     */
    public function getSwoole(): Server
    {
        return $this->swoole;
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
     * @param $name
     * @return ServerAbstract
     */
    public function getListener(string $name): ServerAbstract
    {
        return $this->listens[$name];
    }

    /**
     * @return ServerAbstract[]
     */
    public function getListeners(): array
    {
        return $this->listens;
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
     * @param HandlerInterface $handler
     * @return ServerAbstract
     */
    public function handler(HandlerInterface $handler): ServerAbstract
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * 如果需要自定义自己的swoole服务器,重写此方法
     *
     * @return Server
     */
    public function initSwoole(): Server
    {
        return new Server($this->host, $this->port, SWOOLE_PROCESS, $this->sock_type);
    }

    /**
     * @param $address
     * @param $config
     * @return ServerAbstract
     */
    public static function createServer(string $address = null, array $config = []): ServerAbstract
    {
        return new static($address);
    }

    /**
     * 引导服务，当启动是接收到 swoole server 信息，则默认以这个swoole 服务进行引导
     *
     * @param Server $swoole
     * @return bool
     */
    public function bootstrap(?Server $swoole = null): bool
    {
        if (!$this->isBooted()) {
            $this->swoole = null === $swoole ? $this->initSwoole() : $swoole;

            $this->swoole->set($this->config);

            $handles = get_class_methods($this->handler);
            foreach ($handles as $value) {
                if ('on' == substr($value, 0, 2)) {
                    $this->swoole->on(lcfirst(substr($value, 2)), [$this->handler, $value]);
                }
            }

            $this->booted = true;
        }

        return $this->booted;
    }

    /**
     * @return int
     */
    public function start(): int
    {
        if ($this->isRunning()) {
            output(sprintf('Server <info>[%s] %s:%s</info> address already in use', $this->name, $this->host, $this->port));
        } else {
            try {
                $this->bootstrap();
                if (!is_dir($dir = dirname($this->pid_file))) {
                    mkdir($dir, 0755, true);
                }

                // 多端口监听
                foreach ($this->listens as $listen) {
                    $swoole = $this->swoole->listen($listen->host, $listen->port, $listen->sock_type);
                    $listen->bootstrap($swoole);
                }
                // 进程控制
                foreach ($this->processes as $process) {
                    $this->swoole->addProcess($process->getProcess());
                }

                output(sprintf("Server: <info>%s</info>", $this->name));
                output(sprintf('App version: <info>%s</info>', ServerAbstract::VERSION));
                output(sprintf('Swoole version: <info>%s</info>', SWOOLE_VERSION));
                output(sprintf("Listen <info>%s://%s:%s</info>", $this->protocol, $this->host, $this->port));

                $this->swoole->start();
            } catch (Exception $e) {
                output("<error>{$e->getMessage()}</error>\n");
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
            output(sprintf('Server <info>%s</info> is not running...', $this->name));
            return -1;
        }

        $pid = (int) @file_get_contents($this->getPidFile());
        if (process_kill($pid, SIGTERM)) {
            unlink($this->pid_file);
        }

        output(sprintf('Server <info>%s</info> [<info>#%s</info>] is shutdown...', $this->name, $pid));
        output(sprintf('PID file %s is unlink', $this->pid_file), OutputInterface::VERBOSITY_DEBUG);

        return 0;
    }

    /**
     * @return int
     */
    public function reload(): int
    {
        if (!$this->isRunning()) {
            output(sprintf('Server <info>%s</info> is not running...', $this->name));
            return -1;
        }

        $pid = (int)@file_get_contents($this->getPidFile());

        posix_kill($pid, SIGUSR1);

        output(sprintf('Server <info>%s</info> [<info>%s</info>] is reloading...', $this->name, $pid));

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
            output(sprintf('Server <info>%s</info> is not running...', $this->name));
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

        output(sprintf("Server: <info>%s</info>", $this->name));
        output(sprintf('App version: <info>%s</info>', Server::VERSION));
        output(sprintf('Swoole version: <info>%s</info>', SWOOLE_VERSION));
        output(sprintf("PID file: <info>%s</info>, PID: <info>%s</info>", $this->pid_file, (int) @file_get_contents($this->pid_file)) . PHP_EOL);
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
            output(sprintf('Watching directory: ["<info>%s</info>"]', realpath($directory)));
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
}
