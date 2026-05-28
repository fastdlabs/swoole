<?php

declare(strict_types=1);

use FastD\Swoole\SwooleEventDispatcher;
use FastD\Event\ListenerProvider;
use PHPUnit\Framework\TestCase;

/**
 * SwooleEventDispatcher 测试
 */
class SwooleEventDispatcherTest extends TestCase
{
    private SwooleEventDispatcher $dispatcher;

    protected function setUp(): void
    {
        $this->dispatcher = new SwooleEventDispatcher(new ListenerProvider());
    }

    public function testConstructor()
    {
        // 测试自定义参数
        $customDispatcher = new SwooleEventDispatcher(new ListenerProvider());
        $this->assertNotNull($customDispatcher);
    }

    public function testForward()
    {
        // 测试事件转发
        $mockObject = new stdClass();
        $signo = SIGINT;
        $args = ['code' => 0, 'signal' => SIGINT];

        // 测试转发事件（不抛出异常即可）
        $this->assertNull($this->dispatcher->forward('test_event', $mockObject, $signo, $args));
    }
}
