<?php

declare(strict_types=1);

namespace FastD\Swoole\Listener\Process;

use FastD\Event\EventListenerInterface;
use FastD\Swoole\Event\Process\SignoEvent;

class SignoEventListener implements EventListenerInterface
{

    public function listen(): iterable
    {
        return [
            SignoEvent::class,
        ];
    }

    public function process(object $event): void
    {
        print_r($event);
    }

    public function priority(): int
    {
        return 0;
    }
}