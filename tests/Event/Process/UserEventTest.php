<?php

declare(strict_types=1);

use FastD\Swoole\Event\Process\UserEvent;
use FastD\Swoole\Process\Worker;
use PHPUnit\Framework\TestCase;

/**
 * UserEvent 测试
 */
class UserEventTest extends TestCase
{
    public function testConstructor()
    {
        // 创建 Worker 实例
        $worker = new class('test_worker') extends Worker {
            public function process(Worker $worker): void {}
        };
        $signo = SIGUSR1;
        $args = [['code' => 0, 'signal' => SIGUSR1]];

        // 创建事件
        $event = new UserEvent('user', $worker, $signo, $args);

        // 测试属性
        $this->assertEquals('user', $event->event);
        $this->assertEquals($worker, $event->object);
        $this->assertEquals($signo, $event->signo);
        $this->assertIsArray($event->args);
    }
}
