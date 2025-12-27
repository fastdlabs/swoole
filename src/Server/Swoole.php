<?php

declare(strict_types=1);

namespace FastD\Swoole\Server;

use FastD\Swoole\Server\Callback\CallbackInterface;
use RuntimeException;
use Swoole\Process;
use Throwable;
use Swoole\Server;

abstract class Swoole implements CallbackInterface
{
    protected Server $swoole;

    protected string $protocol = 'tcp';

    protected string $name = 'swoole';

    protected string $host = '127.0.0.1';

    protected int $port = 9527;

    protected string $pid_file = '/tmp/swoole.pid';

    protected array $config = [
        'worker_num'        => 1,
        'open_cpu_affinity' => true,
        'pid_file'          => '/tmp/swoole.pid',
        'max_request'       => 0,
        'reload_async'      => true,
        'user'              => 'www',
        'group'             => 'www',
    ];

    protected array $listens = [];

    protected array $processes = [];

    protected bool $booted = false;

    protected string $handle;

    public function __construct(string $url = 'http://127.0.0.1:9527', protected int $mode = SWOOLE_PROCESS, protected int $sockType = SWOOLE_SOCK_TCP)
    {
        ['scheme' => $scheme, 'host' => $host, 'port' => $port] = parse_url($url);
        $this->protocol = $scheme;
        $this->host = $host;
        $this->port = $port;
    }

    public function configure(array $config): self
    {
        $this->config = array_merge($this->config, $config);

        isset($this->config['pid_file']) && $this->pid_file = $this->config['pid_file'];

        return $this;
    }

    public function rename(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function daemon(): self
    {
        $this->config['daemonize'] = true;

        return $this;
    }

    /**
     * @return Server
     */
    abstract public function createSwooleServer(): \Swoole\Server;

    public function getSwooleServer(): Server
    {
        return $this->swoole;
    }

    public function handle(string $handle): Swoole
    {
        $this->handle = $handle;

        return $this;
    }

    public function bootstrap(): bool
    {
        if (!$this->isBooted()) {
            $this->targetDirectory();
            $this->swoole = $this->createSwooleServer();
            $this->swoole->set($this->config);
            $this->handleCallback();
            $this->booted = true;
        }
        return $this->booted;
    }

    protected function targetDirectory(): void
    {
        if (!is_dir($dir = dirname($this->pid_file))) {
            if (!mkdir($dir, 0755, true)) {
                throw new RuntimeException("Create directory {$dir} failed.");
            }
        }
    }

    protected function handleCallback(): void
    {
        foreach (static::CALLBACK as $value) {
            $this->swoole->on(substr($value, 2), [$this, $value]);
        }
    }

    public static function create(string $url, int $mode = SWOOLE_PROCESS, int $sock_type = SWOOLE_SOCK_TCP): Swoole
    {
        return new static($url, $mode, $sock_type);
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }

    public function isRunning(): bool
    {
        if (file_exists($this->config['pid_file'])) {
            return posix_kill((int)file_get_contents($this->config['pid_file']), 0);
        }

        if ($is_running = process_is_running("{$this->name} master")) {
            $is_running = port_is_running($this->port);
        }

        return $is_running;
    }


    public function start(): bool
    {
        return $this->swoole->start();
    }

    public function stop(): bool
    {
        if (!$this->isRunning()) {
            return false;
        }

        $pid = (int) @file_get_contents($this->pid_file);
        if (($result = process_kill($pid, SIGTERM))) {
            unlink($this->pid_file);
            return $result;
        }

        return false;
    }

    public function reload(): bool
    {
        if (!$this->isRunning()) {
            return false;
        }

        $pid = (int)@file_get_contents($this->pid_file);

        return posix_kill($pid, SIGUSR1);
    }

    public function restart(): bool
    {
        $this->stop();

        return $this->start();
    }

    public function status(): int
    {
        if (!$this->isRunning()) {
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
    }
}
