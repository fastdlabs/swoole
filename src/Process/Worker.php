<?php

declare(strict_types=1);

namespace FastD\Swoole;

use FastD\Swoole\Process\Communication\CommunicationInterface;
use FastD\Swoole\Process\Communication\Queue;

/**
 * 只负责具体逻辑
 */
abstract class Process
{
    public readonly \Swoole\Process $process;

    const STATUS_INIT = 'unknown';
    const STATUS_RUNNING = 'running';
    const STATUS_STOPPED = 'stopped';
    const STATUS_EXCEPTION = 'exception';

    private string $status = 'unknown'; 

    public function __construct(
        public readonly string $name,
        public readonly bool $enableCoroutine = false,
        public readonly ?CommunicationInterface $communication = null // 进程间通信工具
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
        if ($this->process->pid <= 0) {
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

    protected function bootstrap(): void
    {
        $pipeType = match (true) {
            $this->communication instanceof Queue => 1,
            default => null,
        };

        // 通过通信模式判断类型
        $this->process = new \Swoole\Process(
            function (\Swoole\Process $process) {
                try {
                    $this->process($process);
                } catch (\Throwable $e) {
                    $this->status = self::STATUS_EXCEPTION;
                    $process->exit(1);
                }
            },
            !(($this->communication === null)),
            $this->communication === null ? 0 : $this->communication->getIPCType(), // 获取通信模型
            $this->enableCoroutine
        );

        $this->process->name($this->name);
    }

    abstract public function process(\Swoole\Process $process): void;

    public function status(): string
    {
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
            return $pid;
        } catch (\Throwable $e) {
            throw new \RuntimeException("Error starting process: " . $e->getMessage(), 0, $e);
        }
    }

    public function stop(int $signal = SIGTERM): bool
    {
        if ($this->isRunning()) {
            \Swoole\Process::kill($this->getPid(), $signal);
        }
        $this->status = self::STATUS_STOPPED;
        return true;
    }

    public function __invoke()
    {
        return $this->start();
    }
}
