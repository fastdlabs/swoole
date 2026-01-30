<?php

declare(strict_types=1);

use FastD\Swoole\Server;
use PHPUnit\Framework\TestCase;

/**
 * Server 类测试
 */
class ServerTest extends TestCase
{
    public function testConstructor()
    {
        // 测试默认参数
        $defaultServer = new class([]) extends Server {
            public function doStart() {}
        };
        $this->assertNotNull($defaultServer);

        // 测试自定义参数
        $customServer = new class([
            'host' => '0.0.0.0',
            'port' => 8080,
            'mode' => SWOOLE_PROCESS,
            'sock_type' => SWOOLE_SOCK_TCP
        ]) extends Server {
            public function doStart() {}
        };
        $this->assertNotNull($customServer);
    }
}

