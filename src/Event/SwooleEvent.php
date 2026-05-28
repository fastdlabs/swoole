<?php

declare(strict_types=1);

namespace FastD\Swoole\Event;

use FastD\Event\Event;
use FastD\Swoole\Process;
use FastD\Swoole\Server;

class SwooleEvent extends Event
{
    public readonly array $args;

    public function __construct(
        public readonly string $event,
        public readonly object $object, // 事件触发对象
        ...$args // 触发参数，多个...，通过下标调用，顺序与 swoole 回调参数顺序一致
    )
    {
        $this->args = $args;
    }
}