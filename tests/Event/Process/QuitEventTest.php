<?php

declare(strict_types=1);

use FastD\Swoole\Event\Process\QuitEvent;
use FastD\Swoole\Process\Worker;
use PHPUnit\Framework\TestCase;

/**
 * QuitEvent 测试
 */
class QuitEventTest extends TestCase
{
    public function testConstructor()
    {
        // 创建 Worker 实例
        $worker = new class('test_worker') extends Worker {
            public function process(Worker $worker): void {}
        };
        $signo = SIGTERM;
        $args = [['code' => 0, 'signal' => SIGTERM]];

        // 创建事件
        $event = new QuitEvent('quit', $worker, $signo, $args);

        // 测试属性
        $this->assertEquals('quit', $event->event);
        $this->assertEquals($worker, $event->object);
        $this->assertEquals($signo, $event->signo);
        $this->assertIsArray($event->args);
    }
}
