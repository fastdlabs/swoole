<?php

declare(strict_types=1);

namespace FastD\Swoole\Event\Server;

use FastD\Swoole\Event\SwooleEvent;
use FastD\Swoole\Process\Worker;
use FastD\Swoole\Server;

class ServerEvent extends SwooleEvent
{
    public function __construct(
        string $event,
        Server $server,
        public readonly array $ports,
        ...$args)
    {
        parent::__construct($event, $server, ...$args);
    }
}