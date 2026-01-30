<?php

declare(strict_types=1);

namespace FastD\Swoole;

use FastD\Event\EventDispatcher;
use FastD\Event\EventListenerInterface;
use FastD\Event\ListenerProvider;
use FastD\Swoole\Process\IPC\IPCInterface;
use FastD\Swoole\Process\Worker;
use Swoole\Event;
use Swoole\Timer;

class Process extends Worker
{
    protected bool $exit = false;
    
    protected bool $daemon = false;

    protected array $workers = [];

    protected array $workerPids = [];

    protected array $workerStatus = [];

    public function __construct(
        public readonly SwooleEventDispatcher $eventDispatcher = new SwooleEventDispatcher(new ListenerProvider()),
        bool                            $enableCoroutine = false,
        ?IPCInterface                   $communication = null // 进程间通信工具
    )
    {
        parent::__construct('worker-manager', $enableCoroutine, $communication);
    }

    public function addWorker(Worker ...$workers): void
    {
        foreach ($workers as $worker) {
            $this->workers[$worker->name] = $worker;
        }
    }

    public function getWorker(string|int $nameOrPid): ?Worker
    {
        if (is_int($nameOrPid)) {
            $nameOrPid = $this->workerPids[$nameOrPid] ?? '';
        }
        return $this->workers[$nameOrPid] ?? null;
    }

    public function getWorkers(): array
    {
        return $this->workers;
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

    public function process(Worker $worker): void
    {
        // 统一注册信号处理器
        $this->registerSignalListener();

        $this->pid = getmypid();

        // 创建子进程
        foreach ($this->workers as $name => $worker) {
            $pid = $worker->start();
            $this->workerPids[$pid] = $name;
            $this->workerStatus[$worker->status()][$pid] = $name;
        }

        Timer::tick(100, function () {
            // 检查是否应该退出（收到退出信号）并且将自身信号事件投递出去
            if ($this->exit) {
                $this->eventDispatcher->forward('SIGUSR1', $this, SIGUSR1, ...['pid' => $this->pid, 'code' => 0, 'signal' => SIGUSR1]);
                Timer::clearAll();
            }
        });

        Event::wait();
    }

    /**
     * 注册所有信号处理器
     */
    private function registerSignalListener(): void
    {
        // 监听子进程相关信号
        foreach ([SIGPIPE => 'SIGPIPE', SIGTERM => 'SIGTERM', SIGCHLD => 'SIGCHLD'] as $signo => $eventName) {
            \Swoole\Process::signal($signo, function ($signo) use ($eventName) {
                while ($ret = \Swoole\Process::wait(false)) {
                    $childWorker = $this->getWorker($ret['pid']);
                    if ($childWorker !== null) {
                        $this->eventDispatcher->forward($eventName, $childWorker, $signo, ...$ret);
                        // 更新工作进程状态
                        if (isset($this->workerStatus[Worker::STATUS_RUNNING][$ret['pid']])) {
                            unset($this->workerStatus[Worker::STATUS_RUNNING][$ret['pid']]);
                        }
                        $this->workerStatus[$childWorker->status()][$ret['pid']] = $childWorker->name;
                    }
                }
            });
        }

        // 监听终止信号
        foreach ([SIGTERM => 'SIGTERM', SIGINT => 'SIGINT'] as $signo => $eventName) {
            \Swoole\Process::signal($signo, function ($signo) use ($eventName) {
                $this->exit = true;
                $this->eventDispatcher->forward($eventName, $this, $signo, ...['pid' => $this->pid, 'code' => 0, 'signal' => $signo]);
            });
        }
    }

    public function daemon(): void
    {
        parent::daemon();
        $this->daemon = true;
    }

    public function start(): int
    {
        $pid = parent::start();
        if (!$this->daemon) {
            \Swoole\Process::wait(true);
        }
        return $pid;
    }
}