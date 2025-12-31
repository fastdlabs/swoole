<?php

declare(strict_types=1);

namespace FastD\Swoole\Server;

use FastD\Swoole\EventHandler\TCPEventInterface;
use FastD\Swoole\EventHandler\WorkerEventInterface;
use RuntimeException;
use Swoole\Process;
use Swoole\Server;

abstract class Swoole implements WorkerEventInterface
{
    protected Server $swoole;

    protected string $protocol = 'tcp';

    protected string $name = 'swoole';

    protected string $host = '127.0.0.1';

    protected int $port = 9527;

    protected string $pidFile = '/tmp/swoole.pid';

    protected array $config = [
        'worker_num'        => 1,
        'open_cpu_affinity' => true,
        'pid_file'          => '/tmp/swoole.pid',
        'max_request'       => 0,
        'reload_async'      => true,
        'group'             => 'www',
        'user'              => 'www',
    ];

    protected array $callbacks = [];

    protected bool $booted = false;

    public function __construct(string $url = 'http://127.0.0.1:9527', protected int $mode = SWOOLE_PROCESS, protected int $sockType = SWOOLE_SOCK_TCP)
    {
        $parsed = parse_url($url);
        if ($parsed === false) {
            throw new RuntimeException("Invalid URL: {$url}");
        }
        
        ['scheme' => $scheme, 'host' => $host, 'port' => $port] = $parsed;
        $this->protocol = $scheme;
        $this->host = $host;
        $this->port = $port;
        $this->swoole = $this->createSwooleServer($this->protocol, $this->host, $this->port, $this->mode, $this->sockType);
    }

    public function configure(array $config): self
    {
        $this->config = array_merge($this->config, $config);

        isset($this->config['pid_file']) && $this->pidFile = $this->config['pid_file'];

        $this->swoole->set($this->config);

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

    abstract public function createSwooleServer(string $protocol, string $host, int $port, int $mode, int $sockType): Server;

    public function getSwooleServer(): Server
    {
        return $this->swoole;
    }

    public function on(string $event, callable $callback): self
    {
        $this->callbacks[$event] = $callback;

        return $this;
    }

    protected function handlerCallback(): void
    {
        $callbacks = [];
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if ($method !== 'on' && str_starts_with($method, 'on')) {
                $callbacks[strtolower(substr($method, 2))] = [$this, $method];
            }
        }
        $callbacks = array_merge($callbacks, $this->callbacks);
        foreach ($callbacks as $event => $callback) {
            $this->swoole->on($event, $callback);
        }
    }

    protected function targetPidFile(): bool
    {
        if (!is_dir($dir = dirname($this->pidFile))) {
            if (!mkdir($dir, 0755, true)) {
                throw new RuntimeException("Create directory {$dir} failed.");
            }
        }
        return touch($this->pidFile);
    }

    public function bootstrap(): bool
    {
        if (!$this->isBooted()) {
            $this->targetPidFile();
            $this->handlerCallback();
            $this->booted = true;
        }
        return $this->booted;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }

    public function start(): bool
    {
        if (!$this->isBooted()) {
            $this->bootstrap();
        }

        return $this->swoole->start();
    }

    public function stop(): bool
    {
        if (!$this->status()) {
            return false;
        }

        if (!file_exists($this->pidFile)) {
            return false;
        }

        $pid = (int)file_get_contents($this->pidFile);

        return Process::kill($pid, SIGTERM);
    }

    public function reload(): bool
    {
        if (!$this->status()) {
            return false;
        }

        $pid = (int)@file_get_contents($this->pidFile);

        return Process::kill($pid, SIGUSR1);
    }

    public function restart(): bool
    {
        $this->stop();

        return $this->start();
    }

    public function status(): bool
    {
        if (!file_exists($this->pidFile)) {
            return false;
        }

        if (file_exists($this->config['pid_file'])) {
            $pid = (int)file_get_contents($this->pidFile);
            return Process::kill($pid, 0);
        }

        $scriptName = pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_BASENAME);

        $command = "ps axu | grep '{$this->name}' | grep -v grep | grep -v {$scriptName}";

        $output = shell_exec($command);

        if ($output === null) {
            return false;
        }

        return !empty(trim($output));
    }
}