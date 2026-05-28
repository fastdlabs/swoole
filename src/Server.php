<?php

declare(strict_types=1);

namespace FastD\Swoole;

use FastD\Event\EventDispatcher;
use FastD\Event\EventListenerInterface;
use FastD\Event\ListenerProvider;
use FastD\Swoole\Listener\SwooleEventListener;
use RuntimeException;
use Swoole\Process;

class Server
{
    public \Swoole\Server $swoole;

    public string $name = 'swoole-server';

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

    public array $listens = [];

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

        isset($this->setting['pid_file']) && $this->pidFile = $this->setting['pid_file'];

        return $this;
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


    public function getPid(): int
    {
        if (!file_exists($this->pidFile)) {
            return 0;
        }

        return (int)file_get_contents($this->pidFile);
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }

    /**
     * 记录监听端口，并且对应事件会同步到事件调度中
     *
     * @param string $host
     * @param int $port
     * @param EventListenerInterface $eventListener
     * @param string $protocol
     * @param int $mode
     * @param int $sockType
     * @return void
     */
    public function listen(
        string $host,
        int $port,
        SwooleEventListener $eventListener,
        string $protocol = 'http',
        int $mode = SWOOLE_PROCESS,
        int $sockType = SWOOLE_SOCK_TCP,
    ): void
    {
        $this->listens[] = [
            'protocol' => $protocol,
            'host' => $host,
            'port' => $port,
            'mode' => $mode,
            'type' => $sockType,
            'listener' => $eventListener,
        ];
        $eventListener->protocol = $protocol;
        $eventListener->host = $host;
        $eventListener->port = $port;

        $this->addListener($eventListener);
    }

    /**
     * 开放事件监听入口
     *
     * @param EventListenerInterface ...$listener
     * @return void
     */
    public function addListener(EventListenerInterface ...$listener): void
    {
        $this->eventDispatcher->listenerProvider->addListener(...$listener);
    }

    public function bootstrap(): bool
    {
        if (!$this->isBooted()) {
            $this->touchPidFile();
            if (empty($this->listens)) {
                throw new RuntimeException('No listen port configured.');
            }

            $listens = $this->listens;
            $master = array_shift($listens);

            [$this->swoole, $events] = $this->createSwooleServer($master['protocol'], $master['host'], $master['port'], $master['mode'], $master['type']);
            $this->swoole->set($this->setting);
            foreach ($events as $event) {
                $this->swoole->on($event, fn (...$args) => $this->eventDispatcher->forward($event, $this, $this->listens, ...$args));
            }

            foreach ($listens as $listen) {
                $this->swoole->listen($listen['host'], $listen['port'], $listen['type']);
            }

            $this->booted = true;
        }
        return $this->booted;
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
            'udp' => [new \Swoole\Server($host, $port, $mode, $sockType), array_merge($events, ['receive', 'packet',])],
            default => [new \Swoole\Server($host, $port, $mode, $sockType), array_merge($events, ['connect', 'receive', 'close'])],
        };
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
        return Process::kill($this->getPid(), SIGTERM);
    }

    public function reload(): bool
    {
        return Process::kill($this->getPid(), SIGUSR1);
    }

    public function status(): bool
    {
        return Process::kill($this->getPid(), 0);
    }
}