<?php

declare(strict_types=1);

namespace FastD\Swoole\Process\IPC;

interface IPCInterface
{
    /**
     * 初始化通信
     */
    public function init(): bool;

    /**
     * 写入数据
     */
    public function write(mixed $data): bool;

    /**
     * 读取数据
     */
    public function read(int $length = 65536): mixed;

    /**
     * 关闭通信
     */
    public function close(): bool;
}