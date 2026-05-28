<?php

declare(strict_types=1);

namespace FastD\Swoole\Process\IPC;

abstract class IPC implements IPCInterface
{
    // IPC 类型常量
    public const IPC_TYPE_MEMORY = 1;
    public const IPC_TYPE_QUEUE = 2;
    public const IPC_TYPE_SOCKET = 3;

    /**
     * 获取通信模式
     */
    abstract public function getMode(): int;

    /**
     * 获取IPC类型
     */
    abstract public function getIPCType(): int;
}