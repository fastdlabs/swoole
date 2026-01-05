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
        'display_errors'    => true,
        'log_file'          => '/tmp/swoole.log',
        'log_date_format'   => '%Y-%m-%d %H:%M:%S',
//        'log_rotation'      => '日志切割不建议由 swoole 执行，可将切割能力转移到服务器执行',
    ];

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
    }

    public function configure(array $config): self
    {
        $this->config = array_merge($this->config, $config);

        isset($this->config['pid_file']) && $this->pidFile = $this->config['pid_file'];

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

    public function createSwooleServer(string $protocol, string $host, int $port, int $mode, int $sockType): Server
    {
        return match ($protocol) {
            'http' => new \Swoole\Http\Server($host, $port, $mode, $sockType),
            'ws' => new \Swoole\WebSocket\Server($host, $port, $mode, $sockType),
            default => new Server($host, $port, $mode, $sockType),
        };
    }

    public function getSwooleServer(): Server
    {
        return $this->swoole;
    }

    protected function handleCallback(): void
    {
        $methods = get_class_methods($this);
        $ignore = ['on', 'onResponse', 'onException'];
        foreach ($methods as $method) {
            if (!in_array($method, $ignore) && str_starts_with($method, 'on')) {
                $this->swoole->on(strtolower(substr($method, 2)), [$this, $method]);
            }
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
            $this->swoole = $this->createSwooleServer($this->protocol, $this->host, $this->port, $this->mode, $this->sockType);
            $this->swoole->set($this->config);
            $this->handleCallback();
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
        if (!file_exists($this->pidFile) || !$this->status()) {
            return false;
        }

        $pid = (int)file_get_contents($this->pidFile);
        return Process::kill($pid, SIGTERM);
    }

    public function reload(): bool
    {
        if (!file_exists($this->pidFile) || !$this->status()) {
            return false;
        }

        $pid = (int)file_get_contents($this->pidFile);
        return Process::kill($pid, SIGUSR1);
    }

    public function status(): bool
    {
        if (!file_exists($this->pidFile)) {
            return false;
        }

        $pid = (int)file_get_contents($this->pidFile);
        return Process::kill($pid, 0);
    }

    public function onStart(Server $server): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] \033[32m[START]\033[0m Server started at \033[36m{$this->protocol}://{$this->host}:{$this->port}\033[0m with \033[33m{$this->config['worker_num']}\033[0m worker(s)\n";
    }

    public function onBeforeShutdown(Server $server): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] \033[33m[SHUTDOWN]\033[0m Preparing to shutdown server at \033[36m{$this->host}:{$this->port}\033[0m\n";
        echo "[" . date('Y-m-d H:i:s') . "]        Process ID: \033[36m" . getmypid() . "\033[0m\n";
    }

    public function onShutdown(Server $server): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] \033[31m[MASTER SHUTDOWN]\033[0m Server shutdown completed at \033[36m{$this->protocol}://{$this->host}:{$this->port}\033[0m\n";
    }

    public function onManagerStart(Server $server): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] \033[35m[MANAGER]\033[0m Manager process started\n";
    }

    public function onManagerStop(Server $server): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] \033[35m[MANAGER]\033[0m Manager process stopped\n";
    }

    public function onWorkerStart(Server $server, int $id): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] \033[36m[WORKER]\033[0m Worker \033[33m#{$id}\033[0m started\n";
    }

    public function onWorkerStop(Server $server, int $id): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] \033[36m[WORKER]\033[0m Worker \033[33m#{$id}\033[0m stopped\n";
    }

    public function onWorkerError(Server $server, int $id, int $workerPid, int $exitCode, int $signal): void
    {
        $exitMessage = match ($exitCode) {
            0 => 'Normal exit',
            1 => 'General error',
            2 => 'Misuse of shell command',
            126 => 'Command cannot execute',
            127 => 'Command not found',
            128 => 'Invalid argument to exit',
            130 => 'Script terminated by Control-C',
            132 => 'Illegal instruction',
            133 => 'Trace/breakpoint trap',
            134 => 'Aborted',
            136 => 'Floating point exception',
            137 => 'Killed (often sent by system when OOM)',
            139 => 'Segmentation fault',
            143 => 'Terminated (SIGTERM)',
            default => 'Unknown exit code'
        };

        $signalMessage = match ($signal) {
            0 => 'No signal',
            1 => 'SIGHUP - Hangup (control terminal)',
            2 => 'SIGINT - Interrupt (Ctrl+C)',
            3 => 'SIGQUIT - Quit (Ctrl+\\)',
            6 => 'SIGABRT - Abort signal',
            9 => 'SIGKILL - Kill signal',
            11 => 'SIGSEGV - Invalid memory reference',
            13 => 'SIGPIPE - Broken pipe',
            14 => 'SIGALRM - Timer signal',
            15 => 'SIGTERM - Termination signal',
            default => "Signal {$signal}"
        };

        echo "[" . date('Y-m-d H:i:s') . "] \033[31m[CRITICAL]\033[0m Worker \033[33m#{$id}\033[0m (PID: {$workerPid}) \033[31mUNEXPECTEDLY EXITED\033[0m\n";
        echo "[" . date('Y-m-d H:i:s') . "]          Exit Code: \033[31m{$exitCode}\033[0m (\033[31m{$exitMessage}\033[0m)\n";
        echo "[" . date('Y-m-d H:i:s') . "]          Signal: \033[31m{$signal}\033[0m (\033[31m{$signalMessage}\033[0m)\n";
        echo "[" . date('Y-m-d H:i:s') . "]          Server: \033[36m{$this->host}:{$this->port}\033[0m\n";
    }

    public function onWorkerExit(Server $server, int $id): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] \033[35m[EXIT]\033[0m Worker \033[33m#{$id}\033[0m exited normally\n";
    }

    public function onBeforeReload(Server $server): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] \033[33m[WORKER BEFORE RELOAD]\033[0m Preparing to reload\n";
    }

    public function onAfterReload(Server $server): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] \033[32m[WORKER AFTER RELOAD]\033[0m Workers reloaded successfully\n";
    }
}