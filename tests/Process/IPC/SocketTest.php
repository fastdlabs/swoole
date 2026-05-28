<?php

declare(strict_types=1);

use FastD\Swoole\Process\IPC\Socket;
use PHPUnit\Framework\TestCase;

/**
 * Socket IPC 实现测试
 */
class SocketTest extends TestCase
{
    private Socket $socket;

    protected function setUp(): void
    {
        $this->socket = new Socket('127.0.0.1', 0, SWOOLE_SOCK_TCP);
    }

    public function testConstructor()
    {
        // 测试默认参数
        $defaultSocket = new Socket();
        $this->assertNotNull($defaultSocket);

        // 测试自定义参数
        $customSocket = new Socket('127.0.0.1', 9501, SWOOLE_SOCK_TCP);
        $this->assertNotNull($customSocket);

        // 测试服务器模式
        $serverSocket = new Socket('127.0.0.1', 9502, SWOOLE_SOCK_TCP, true);
        $this->assertNotNull($serverSocket);
    }

    public function testClose()
    {
        // 测试关闭
        $result = $this->socket->close();
        $this->assertTrue($result);
    }

    public function testGetMode()
    {
        // 测试获取模式
        $mode = $this->socket->getMode();
        $this->assertEquals(SWOOLE_SOCK_TCP, $mode);
    }

    public function testGetIPCType()
    {
        // 测试获取 IPC 类型
        $ipcType = $this->socket->getIPCType();
        $this->assertEquals(Socket::IPC_TYPE_SOCKET, $ipcType);
    }
}
