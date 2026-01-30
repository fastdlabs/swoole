<?php

declare(strict_types=1);

namespace FastD\Swoole\Process\IPC;

/**
 * 队列通信实现
 */
class Queue extends IPC
{
    protected bool $enabled = false;
    protected int $msgKey;
    protected int $mode;
    protected int $capacity;
    protected ?\Swoole\Process $process = null;

    public function __construct(int $msgKey = 0, int $mode = SWOOLE_MSGQUEUE_BALANCE, int $capacity = -1)
    {
        $this->msgKey = $msgKey ?: ftok(__FILE__, 'a');
        $this->mode = $mode;
        $this->capacity = $capacity;
    }

    public function setProcess(\Swoole\Process $process): void
    {
        $this->process = $process;
    }

    public function init(): bool
    {
        if ($this->enabled || !$this->process) {
            return false;
        }

        $result = $this->process->useQueue($this->msgKey, $this->mode, $this->capacity);
        if ($result) {
            $this->enabled = true;
        }

        return $result;
    }

    public function write(mixed $data): bool
    {
        if (!$this->enabled) {
            $this->init();
        }
        return $this->process->push(serialize($data));
    }

    public function read(int $length = 65536): mixed
    {
        if (!$this->enabled) {
            $this->init();
        }
        
        $content = $this->process->pop($length);
        if ($content === false) {
            return false;
        }
        return unserialize($content);
    }

    public function close(): bool
    {
        if ($this->enabled && $this->process) {
            return $this->process->freeQueue();
        }
        return true;
    }

    public function getMode(): int
    {
        return $this->mode;
    }

    public function getIPCType(): int
    {
        return self::IPC_TYPE_QUEUE;
    }
}