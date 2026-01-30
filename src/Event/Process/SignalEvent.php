<?php

declare(strict_types=1);

namespace FastD\Swoole\Event\Process;

use FastD\Swoole\Event\SwooleEvent;
use FastD\Swoole\Process\Worker;

class SignalEvent extends SwooleEvent
{
    public function __construct(
        string $event,
        Worker $worker,
        public readonly int $signo,
        ...$args)
    {
        parent::__construct($event, $worker, ...$args);
    }
}