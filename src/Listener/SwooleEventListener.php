<?php

declare(strict_types=1);

namespace FastD\Swoole\Listener;

use FastD\Event\EventListenerInterface;
use FastD\Swoole\Event\SwooleEvent;

abstract class SwooleEventListener implements EventListenerInterface
{
    public string $protocol;
    public string $host;
    public int $port;

    abstract public function listen(): iterable;

    abstract public function process(object $event): void;

    public function priority(): int
    {
        return 0;
    }
}