<?php

declare(strict_types=1);

namespace FastD\Swoole\Process\IPC;

interface CommunicationInterface
{
    /**
     * 获取通信模式
     */
    public function getMode(): int;

    /**
     * 获取IPC类型
     */
    public function getIPCType(): int;

    /**
     * 写入数据
     */
    public function write(mixed $data): bool;

    /**
     * 读取数据
     */
    public function read(int $length = 65536): mixed;

    /**
     * 初始化通信
     */
    public function init(): bool;

    /**
     * 关闭通信
     */
    public function close(): bool;
}