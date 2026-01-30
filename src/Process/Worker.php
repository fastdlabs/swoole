<?php

declare(strict_types=1);

namespace FastD\Swoole\Process;

use FastD\Swoole\Process\IPC\IPCInterface;
use FastD\Swoole\Process\IPC\Queue;
use Throwable;

/**
 * 只负责具体逻辑
 */
abstract class Worker
{
    public readonly \Swoole\Process $process;

    const STATUS_RUNNING = 'running';
    const STATUS_STOPPED = 'stopped';
    const STATUS_EXCEPTION = 'exception';

    protected string $status = 'unknown';

    protected int $pid = 0;

    public function __construct(
        public readonly string        $name,
        public readonly bool          $enableCoroutine = false,
        public readonly ?IPCInterface $communication = null // 进程间通信工具
    )
    {
    }

    public function getPid(): int
    {
        return $this->process->pid;
    }

    public function daemon(): void
    {
        $this->process->daemon();
    }

    public function isRunning(): bool
    {
        if (!isset($this->process) || $this->process->pid <= 0) {
            return false;
        }
        
        // 使用 kill 检查进程是否存在
        return \Swoole\Process::kill($this->process->pid, 0);
    }

    public function write(string $data): bool
    {
        if ($this->isRunning()) {
            return $this->process->write($data) !== false;
        }
        return false;
    }

    public function read(int $size = 8192): ?string
    {
        if ($this->isRunning()) {
            $data = $this->process->read($size);
            return $data !== false ? $data : null;
        }
        return null;
    }

    /**
     * 获取 IPC 实例
     */
    public function getIPC(): ?IPCInterface
    {
        return $this->communication;
    }

    /**
     * 通过 IPC 写入数据
     */
    public function ipcWrite(mixed $data): bool
    {
        if ($this->communication) {
            return $this->communication->write($data);
        }
        return false;
    }

    /**
     * 通过 IPC 读取数据
     */
    public function ipcRead(int $length = 65536): mixed
    {
        if ($this->communication) {
            return $this->communication->read($length);
        }
        return false;
    }

    protected function bootstrap(): void
    {
        // 确定管道类型
        $pipeType = $this->communication ? 1 : 0;

        // 创建进程
        $this->process = new \Swoole\Process(
            fn () => $this->process($this),
            $this->communication !== null,
            $pipeType,
            $this->enableCoroutine
        );

        // 初始化 IPC
        $this->initIPC();

        $this->process->name($this->name);
    }

    /**
     * 初始化 IPC
     */
    protected function initIPC(): void
    {
        if (!$this->communication) {
            return;
        }

        // 对于 Queue 类型，需要设置 Process 实例
        if ($this->communication instanceof Queue) {
            $this->communication->setProcess($this->process);
        }

        // 初始化 IPC
        $this->communication->init();
    }

    abstract public function process(Worker $worker): void;

    public function status(string $status = ''): string
    {
        if ($status !== '') {
            $this->status = $status;
            return $this->status;
        }
        return $this->status;
    }

    public function start(): int
    {
        try {
            $this->bootstrap();
            $pid = $this->process->start();
            if ($pid === false) {
                throw new \RuntimeException("Failed to start process: {$this->name}");
            }
            $this->status = self::STATUS_RUNNING;
            $this->pid = $this->process->pid;
            return $pid;
        } catch (Throwable $e) {
            throw new \RuntimeException("Error starting process: " . $e->getMessage(), 0, $e);
        }
    }

    public function stop(int $signal = SIGTERM, int $status = 0): bool
    {
        if ($this->isRunning()) {
            \Swoole\Process::kill($this->getPid(), $signal);
        }

        // 关闭 IPC
        if ($this->communication) {
            $this->communication->close();
        }

        if ($status === 0) {
            $this->status = self::STATUS_STOPPED;
        } else {
            $this->status = self::STATUS_EXCEPTION;
        }
        return true;
    }
}
