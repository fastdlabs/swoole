<?php

declare(strict_types=1);

namespace FastD\Swoole\Process\IPC;

/**
 * 内存共享通信实现
 */
class Memory extends IPC
{
    protected ?\Swoole\Table $table;
    protected string $name;
    protected int $size;

    public function __construct(string $name = '', int $size = 1024)
    {
        $this->name = $name ?: uniqid('shared_memory_');
        $this->size = $size;
    }

    public function init(): bool
    {
        $this->table = new \Swoole\Table($this->size);
        $this->table->column('data', \Swoole\Table::TYPE_STRING, 2048);
        $this->table->create();
        return true;
    }

    public function write(mixed $data): bool
    {
        $key = md5(serialize($data));
        return $this->table->set($key, ['data' => serialize($data)]);
    }

    public function read(int $length = 65536): mixed
    {
        // 从表中获取第一行数据作为示例
        foreach ($this->table as $key => $row) {
            $data = unserialize($row['data']);
            // 删除已读取的数据
            $this->table->del($key);
            return $data;
        }
        return false;
    }

    public function close(): bool
    {
        // 内存表不需要关闭连接
        return true;
    }

    public function getMode(): int
    {
        return self::IPC_TYPE_MEMORY;
    }

    public function getIPCType(): int
    {
        return self::IPC_TYPE_MEMORY;
    }
}