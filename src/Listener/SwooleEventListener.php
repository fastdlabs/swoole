<?php

declare(strict_types=1);

namespace FastD\Swoole\Listener\Server;

use FastD\Event\EventListenerInterface;
use FastD\Swoole\Event\Server\SwooleEvent;

abstract class SwooleEventListener implements EventListenerInterface
{
    abstract public function listen(): iterable;

    public function process(object $event): void
    {
        if ($event instanceof SwooleEvent) {
            $method = 'on' . ucfirst($event->event);
            if (method_exists($this, $method)) {
                // swoole event 会将整个 Event 传递，如果要延续系统参数，则重写或者实现 EventListenerInterface 接口完成
                // $this->method(...$event->args);
                $this->{$method}($event);
            }
        }
    }

    public function priority(): int
    {
        return 0;
    }
}