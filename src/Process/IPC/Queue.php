<?php

declare(strict_types=1);

namespace FastD\Swoole\Process\Communication;

use FastD\Swoole\Process\Communication;
use FastD\Swoole\Process\CommunicationInterface;
use FastD\Swoole\Process\Worker;

/**
 * 队列通信实现
 */
class Queue extends Communication implements CommunicationInterface
{
    protected bool $enabled = false;
    protected int $msgKey;
    protected int $mode;
    protected int $capacity;

    public function __construct(Worker $process, int $msgKey = 0, int $mode = SWOOLE_MSGQUEUE_BALANCE, int $capacity = -1)
    {
        parent::__construct($process);
        $this->msgKey = $msgKey ?: ftok(__FILE__, 'a');
        $this->mode = $mode;
        $this->capacity = $capacity;
    }

    public function init(): bool
    {
        if ($this->enabled) {
            return false;
        }

        $result = $this->process->process->useQueue($this->msgKey, $this->mode, $this->capacity);
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
        return $this->process->process->push(serialize($data));
    }

    public function read(int $length = 65536): mixed
    {
        if (!$this->enabled) {
            $this->init();
        }
        
        $content = $this->process->process->pop($length);
        if ($content === false) {
            return false;
        }
        return unserialize($content);
    }

    public function close(): bool
    {
        if ($this->enabled) {
            return $this->process->process->freeQueue();
        }
        return true;
    }

    public function getMode(): int
    {
        return $this->mode;
    }

    public function getIPCType(): int
    {
        return $this->msgKey;
    }
}