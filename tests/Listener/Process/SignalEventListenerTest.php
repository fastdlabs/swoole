<?php

declare(strict_types=1);

use FastD\Swoole\Listener\Process\SignalEventListener;
use PHPUnit\Framework\TestCase;

/**
 * SignalEventListener 测试
 */
class SignalEventListenerTest extends TestCase
{
    public function testListen()
    {
        // 创建一个具体的 SignalEventListener 子类
        $listener = new class extends SignalEventListener {
            protected function onUserSignal(object $event): void {}
        };

        // 测试监听的事件
        $events = $listener->listen();
        $this->assertIsArray($events);
        $this->assertCount(3, $events);
        $this->assertContains('FastD\Swoole\Event\Process\SignalEvent', $events);
        $this->assertContains('FastD\Swoole\Event\Process\QuitEvent', $events);
        $this->assertContains('FastD\Swoole\Event\Process\UserEvent', $events);
    }
}
