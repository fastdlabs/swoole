<?php

declare(strict_types=1);

use FastD\Swoole\Process;
use FastD\Swoole\Process\Worker;
use FastD\Swoole\Process\IPC\Memory;
use PHPUnit\Framework\TestCase;

/**
 * Process 类测试
 */
class ProcessTest extends TestCase
{
    private Process $process;

    protected function setUp(): void
    {
        $this->process = new Process(enableCoroutine: true, communication: new Memory());
    }

    public function testConstructor()
    {
        // 测试默认参数
        $defaultProcess = new Process();
        $this->assertNotNull($defaultProcess);

        // 测试自定义参数
        $customProcess = new Process(enableCoroutine: false, communication: new Memory());
        $this->assertNotNull($customProcess);
    }

    public function testAddWorker()
    {
        // 创建 Worker
        $worker = new class('test_worker') extends Worker {
            public function process(Worker $worker): void {}
        };

        // 测试添加 Worker
        $this->process->addWorker($worker);
        $workers = $this->process->getWorkers();
        $this->assertCount(1, $workers);
        $this->assertArrayHasKey('test_worker', $workers);
    }

    public function testGetWorker()
    {
        // 创建 Worker
        $worker = new class('test_worker') extends Worker {
            public function process(Worker $worker): void {}
        };

        // 添加 Worker
        $this->process->addWorker($worker);

        // 测试通过名称获取 Worker
        $retrievedWorker = $this->process->getWorker('test_worker');
        $this->assertNotNull($retrievedWorker);
        $this->assertEquals('test_worker', $retrievedWorker->name);

        // 测试通过 PID 获取 Worker（应该返回 null，因为没有启动）
        $pidWorker = $this->process->getWorker(12345);
        $this->assertNull($pidWorker);
    }

    public function testGetWorkers()
    {
        // 创建多个 Worker
        $worker1 = new class('worker1') extends Worker {
            public function process(Worker $worker): void {}
        };

        $worker2 = new class('worker2') extends Worker {
            public function process(Worker $worker): void {}
        };

        // 添加 Worker
        $this->process->addWorker($worker1, $worker2);

        // 测试获取所有 Worker
        $workers = $this->process->getWorkers();
        $this->assertCount(2, $workers);
        $this->assertArrayHasKey('worker1', $workers);
        $this->assertArrayHasKey('worker2', $workers);
    }
}
