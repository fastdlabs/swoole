<?php

declare(strict_types=1);

namespace FastD\Swoole\Process\IPC;

use FastD\Swoole\Process\Worker;

abstract class Communication implements CommunicationInterface
{
    public function __construct(
        public readonly Worker $process
    )
    {
    }
}