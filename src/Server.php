<?php

declare(strict_types=1);

namespace FastD\Swoole;

use FastD\Event\EventDispatcher;
use FastD\Event\EventListenerInterface;
use FastD\Event\ListenerProvider;
use RuntimeException;
use Swoole\Process;
use Swoole\Server;

class SwooleServer
{
    protected Server $swoole;

    protected string $name = 'swoole-server';

    protected string $pidFile = '/tmp/swoole.pid';

    public array $setting = [
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

    protected array $listens = [];

    public function __construct(
        array $setting = [],
        public readonly EventDispatcher $eventDispatcher = new SwooleEventDispatcher(new ListenerProvider()) // 引入事件调度进行操作，与 fastd 核心中通用
    )
    {
        $this->setting($setting);
    }

    public function setting(array $setting): self
    {
        $this->setting = array_merge($this->setting, $setting);

        isset($this->config['pid_file']) && $this->pidFile = $this->config['pid_file'];

        return $this;
    }

    public function getSetting(): array
    {
        return $this->setting;
    }

    public function rename(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function daemon(): self
    {
        $this->setting['daemonize'] = true;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function listen(string $host, int $port, EventListenerInterface $eventListener): void
    {
        

        $this->eventDispatcher->listenerProvider->addListener($eventListener);
    }

    protected function touchPidFile(): bool
    {
        if (!is_dir($dir = dirname($this->pidFile))) {
            if (!mkdir($dir, 0755, true)) {
                throw new RuntimeException("Create directory {$dir} failed.");
            }
        }
        return touch($this->pidFile);
    }

    protected function createSwooleServer(string $protocol, string $host, int $port, int $mode, int $sockType): array
    {
        $events = ['start', 'beforeShutdown', 'shutdown', 'workerStart', 'workerStop', 'workerError', 'workerExit', 'pipeMessage', 'managerStart', 'managerStop', 'beforeReload', 'afterReload', 'task', 'finish'];
        // 返回服务和回调
        return match ($protocol) {
            'http' => [new \Swoole\Http\Server($host, $port, $mode, $sockType), array_merge($events, ['request'])],
            'ws' => [new \Swoole\WebSocket\Server($host, $port, $mode, $sockType), array_merge($events, ['beforeHandshakeResponse', 'handShake', 'open', 'message', 'request', 'disconnect'])],
            'udp' => [new Server($host, $port, $mode, $sockType), array_merge($events, ['receive', 'packet',])],
            default => [new Server($host, $port, $mode, $sockType), array_merge($events, ['connect', 'receive', 'close'])],
        };
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }

    public function bootstrap(): bool
    {
        if (!$this->isBooted()) {
            $this->touchPidFile();
            [$this->swoole, $events] = $this->createSwooleServer('http', '127.0.0.1', 9527, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
            $this->swoole->set($this->setting);

            array_map(fn(string $event) => $this->swoole->on($event, function (...$args) use ($event) {
                $this->eventDispatcher->forward($event, ...$args);
            }), $events);

            $this->booted = true;
        }
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
}