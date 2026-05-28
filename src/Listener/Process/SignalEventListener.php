<?php

declare(strict_types=1);

namespace FastD\Swoole\Listener\Process;

use FastD\Swoole\Event\Process\QuitEvent;
use FastD\Swoole\Event\Process\SignalEvent;
use FastD\Swoole\Event\Process\UserEvent;
use FastD\Swoole\Listener\SwooleEventListener;
use FastD\Swoole\Process;
use FastD\Swoole\Process\Worker;

abstract class SignalEventListener extends SwooleEventListener
{
    public function listen(): iterable
    {
        return [
            SignalEvent::class,
            QuitEvent::class,
            UserEvent::class,
        ];
    }

    public function process(object $event): void
    {
        $worker = $event->object;
        $signo = $event->signo;
        $exitCode = $event->args['code'];
        match ($event->event) {
            'SIGTERM', 'SIGINT' => $this->broadcastSignal($worker, $signo, $exitCode),
            'SIGCHLD' => $worker instanceof Worker && $worker->stop($signo, $exitCode),
            default => $this->onUserSignal($event) // 不存在的事件
        };
    }

    private function broadcastSignal(Worker $worker, int $signo, int $exitCode): void
    {
        if ($worker instanceof Process) {
            foreach ($worker->getWorkers() as $childrenWorker) {
                $childrenWorker->stop($signo, $exitCode);
            }
        }

        $worker->stop($signo, $exitCode);
    }

    abstract protected function onUserSignal(object $event): void;
}