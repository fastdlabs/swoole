<?php

declare(strict_types=1);

use FastD\Swoole\Process\Worker;
use FastD\Swoole\Process\IPC\Memory;
use PHPUnit\Framework\TestCase;

/**
 * Worker 类测试
 */
class WorkerTest extends TestCase
{
    private Worker $worker;

    protected function setUp(): void
    {
        // 创建一个具体的 Worker 子类
        $this->worker = new class('test_worker', true, new Memory()) extends Worker {
            public function process(Worker $worker): void
            {
                // 空实现
            }
        };
    }

    public function testConstructor()
    {
        // 测试默认参数
        $defaultWorker = new class('default_worker') extends Worker {
            public function process(Worker $worker): void {}
        };
        $this->assertNotNull($defaultWorker);

        // 测试自定义参数
        $customWorker = new class('custom_worker', false, new Memory()) extends Worker {
            public function process(Worker $worker): void {}
        };
        $this->assertNotNull($customWorker);
    }

    public function testStatus()
    {
        // 测试获取默认状态
        $defaultStatus = $this->worker->status();
        $this->assertEquals('unknown', $defaultStatus);

        // 测试设置状态
        $setStatus = $this->worker->status(Worker::STATUS_RUNNING);
        $this->assertEquals(Worker::STATUS_RUNNING, $setStatus);

        // 测试获取状态
        $getStatus = $this->worker->status();
        $this->assertEquals(Worker::STATUS_RUNNING, $getStatus);
    }

    public function testGetIPC()
    {
        // 测试获取 IPC 实例
        $ipc = $this->worker->getIPC();
        $this->assertNotNull($ipc);
        $this->assertInstanceOf(Memory::class, $ipc);
    }

    public function testIpcWriteAndRead()
    {
        // 初始化 IPC
        $ipc = $this->worker->getIPC();
        $ipc->init();

        // 测试数据
        $testData = ['key' => 'value'];

        // 测试 IPC 写入
        $writeResult = $this->worker->ipcWrite($testData);
        $this->assertTrue($writeResult);

        // 测试 IPC 读取
        $readResult = $this->worker->ipcRead();
        $this->assertEquals($testData, $readResult);
    }
}
