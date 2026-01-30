<?php

declare(strict_types=1);

namespace FastD\Swoole\Event\Server;

use FastD\Event\Event;

class SwooleEvent extends Event
{
    public readonly array $args;

    public function __construct(
        public readonly string $event,
        public readonly array $ports,
        ...$args
    )
    {
        $this->args = $args;
    }
}