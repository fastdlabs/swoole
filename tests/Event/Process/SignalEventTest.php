<?php

declare(strict_types=1);

use FastD\Swoole\Event\Process\SignalEvent;
use FastD\Swoole\Process\Worker;
use PHPUnit\Framework\TestCase;

/**
 * SignalEvent 测试
 */
class SignalEventTest extends TestCase
{
    public function testConstructor()
    {
        // 创建 Worker 实例
        $worker = new class('test_worker') extends Worker {
            public function process(Worker $worker): void {}
        };
        $signo = SIGINT;
        $args = [['code' => 0, 'signal' => SIGINT]];

        // 创建事件
        $event = new SignalEvent('SIGINT', $worker, $signo, $args);

        // 测试属性
        $this->assertEquals('SIGINT', $event->event);
        $this->assertEquals($worker, $event->object);
        $this->assertEquals($signo, $event->signo);
        $this->assertIsArray($event->args);
    }
}
