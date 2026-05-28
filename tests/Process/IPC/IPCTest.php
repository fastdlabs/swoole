<?php

declare(strict_types=1);

use FastD\Swoole\Process\IPC\IPC;
use PHPUnit\Framework\TestCase;

/**
 * IPC 抽象类测试
 */
class IPCTest extends TestCase
{
    public function testConstants()
    {
        // 测试 IPC 类型常量定义
        $this->assertEquals(1, IPC::IPC_TYPE_MEMORY);
        $this->assertEquals(2, IPC::IPC_TYPE_QUEUE);
        $this->assertEquals(3, IPC::IPC_TYPE_SOCKET);
    }

    public function testAbstractMethods()
    {
        // 测试抽象方法存在性
        $reflection = new ReflectionClass(IPC::class);
        
        // 检查 getMode 方法是否为抽象方法
        $this->assertTrue($reflection->getMethod('getMode')->isAbstract());
        
        // 检查 getIPCType 方法是否为抽象方法
        $this->assertTrue($reflection->getMethod('getIPCType')->isAbstract());
        
        // 检查 init 方法是否存在（从 IPCInterface 继承）
        $this->assertTrue($reflection->hasMethod('init'));
        
        // 检查 write 方法是否存在（从 IPCInterface 继承）
        $this->assertTrue($reflection->hasMethod('write'));
        
        // 检查 read 方法是否存在（从 IPCInterface 继承）
        $this->assertTrue($reflection->hasMethod('read'));
        
        // 检查 close 方法是否存在（从 IPCInterface 继承）
        $this->assertTrue($reflection->hasMethod('close'));
    }
}
