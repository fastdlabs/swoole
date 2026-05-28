<?php

declare(strict_types=1);

use FastD\Swoole\Process\IPC\Memory;
use PHPUnit\Framework\TestCase;

/**
 * Memory IPC 实现测试
 */
class MemoryTest extends TestCase
{
    private Memory $memory;

    protected function setUp(): void
    {
        $this->memory = new Memory('test_memory', 1024);
    }

    public function testConstructor()
    {
        // 测试默认参数
        $defaultMemory = new Memory();
        $this->assertNotNull($defaultMemory);

        // 测试自定义参数
        $customMemory = new Memory('custom_memory', 2048);
        $this->assertNotNull($customMemory);
    }

    public function testInit()
    {
        // 测试初始化
        $result = $this->memory->init();
        $this->assertTrue($result);
    }

    public function testWriteAndRead()
    {
        // 初始化
        $this->memory->init();

        // 测试数据
        $testData = [
            'key' => 'value',
            'number' => 123,
            'array' => ['a', 'b', 'c']
        ];

        // 写入数据
        $writeResult = $this->memory->write($testData);
        $this->assertTrue($writeResult);

        // 读取数据
        $readResult = $this->memory->read();
        $this->assertEquals($testData, $readResult);
    }

    public function testClose()
    {
        // 测试关闭
        $result = $this->memory->close();
        $this->assertTrue($result);
    }

    public function testGetMode()
    {
        // 测试获取模式
        $mode = $this->memory->getMode();
        $this->assertEquals(Memory::IPC_TYPE_MEMORY, $mode);
    }

    public function testGetIPCType()
    {
        // 测试获取 IPC 类型
        $ipcType = $this->memory->getIPCType();
        $this->assertEquals(Memory::IPC_TYPE_MEMORY, $ipcType);
    }

    public function testReadWithEmptyMemory()
    {
        // 初始化
        $this->memory->init();

        // 读取空内存
        $result = $this->memory->read();
        $this->assertFalse($result);
    }
}
