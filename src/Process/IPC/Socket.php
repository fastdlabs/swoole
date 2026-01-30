<?php

declare(strict_types=1);

namespace FastD\Swoole\Process\Communication;

use FastD\Swoole\Process\Communication;
use FastD\Swoole\Process\CommunicationInterface;
use FastD\Swoole\Process\Worker;

/**
 * Socket 通信实现
 */
class Socket extends Communication implements CommunicationInterface
{
    protected ?\Swoole\Coroutine\Socket $socket = null;
    protected int $port;
    protected string $host;
    protected int $type;

    public function __construct(Worker $process, string $host = '127.0.0.1', int $port = 0, int $type = SWOOLE_SOCK_TCP)
    {
        parent::__construct($process);
        $this->host = $host;
        $this->port = $port;
        $this->type = $type;
    }

    public function init(): bool
    {
        $this->socket = new \Swoole\Coroutine\Socket($this->type);
        if ($this->port > 0) {
            return $this->socket->bind($this->host, $this->port);
        }
        return true;
    }

    public function connect(): bool
    {
        if (!$this->socket) {
            $this->init();
        }
        return $this->socket->connect($this->host, $this->port);
    }

    public function write(mixed $data): bool
    {
        if (!$this->socket || !$this->socket->isConnected()) {
            if (!$this->connect()) {
                return false;
            }
        }
        return $this->socket->send(serialize($data)) !== false;
    }

    public function read(int $length = 65536): mixed
    {
        if (!$this->socket || !$this->socket->isConnected()) {
            return false;
        }
        $data = $this->socket->recv($length);
        if ($data === false) {
            return false;
        }
        return unserialize($data);
    }

    public function close(): bool
    {
        if ($this->socket) {
            return $this->socket->close();
        }
        return true;
    }

    public function getMode(): int
    {
        return $this->type;
    }

    public function getIPCType(): int
    {
        return $this->type;
    }
}