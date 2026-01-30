<?php

declare(strict_types=1);

use FastD\Swoole\Process\IPC\Queue;
use PHPUnit\Framework\TestCase;

/**
 * Queue IPC 实现测试
 */
class QueueTest extends TestCase
{
    private Queue $queue;

    protected function setUp(): void
    {
        $this->queue = new Queue(ftok(__FILE__, 'q'), SWOOLE_MSGQUEUE_BALANCE);
    }

    public function testConstructor()
    {
        // 测试默认参数
        $defaultQueue = new Queue();
        $this->assertNotNull($defaultQueue);

        // 测试自定义参数
        $customQueue = new Queue(ftok(__FILE__, 'c'), SWOOLE_MSGQUEUE_BALANCE, 1024);
        $this->assertNotNull($customQueue);
    }

    public function testSetProcess()
    {
        // 创建模拟的 Swoole\Process
        $mockProcess = $this->createMock(\Swoole\Process::class);

        // 测试设置进程
        $this->queue->setProcess($mockProcess);
        $this->assertNotNull($this->queue);
    }

    public function testClose()
    {
        // 测试关闭
        $result = $this->queue->close();
        $this->assertTrue($result);
    }

    public function testGetMode()
    {
        // 测试获取模式
        $mode = $this->queue->getMode();
        $this->assertEquals(SWOOLE_MSGQUEUE_BALANCE, $mode);
    }

    public function testGetIPCType()
    {
        // 测试获取 IPC 类型
        $ipcType = $this->queue->getIPCType();
        $this->assertEquals(Queue::IPC_TYPE_QUEUE, $ipcType);
    }
}
