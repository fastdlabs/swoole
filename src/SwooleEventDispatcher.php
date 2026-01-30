<?php

declare(strict_types=1);

namespace FastD\Swoole\Server;

use FastD\Event\EventDispatcher;
use FastD\Event\ThrowableEventInterface;
use FastD\Event\ThrowableListener;
use FastD\Swoole\Server\Event\SwooleEvent;
use Psr\EventDispatcher\StoppableEventInterface;

class SwooleEventDispatcher extends EventDispatcher
{
    protected array $serverEvents = [
        'start'             => "\\FastD\\Swoole\\Server\\Event\\SwooleEvent",
        'beforeShutdown'    => "\\FastD\\Swoole\\Server\\Event\\SwooleEvent",
        'shutdown'          => "\\FastD\\Swoole\\Server\\Event\\SwooleEvent",
        'workerStart'       => "\\FastD\\Swoole\\Server\\Event\\SwooleEvent",
        'workerStop'        => "\\FastD\\Swoole\\Server\\Event\\SwooleEvent",
        'workerError'       => "\\FastD\\Swoole\\Server\\Event\\SwooleEvent",
        'workerExit'        => "\\FastD\\Swoole\\Server\\Event\\SwooleEvent",
        'pipeMessage'       => "\\FastD\\Swoole\\Server\\Event\\SwooleEvent",
        'managerStart'      => "\\FastD\\Swoole\\Server\\Event\\SwooleEvent",
        'managerStop'       => "\\FastD\\Swoole\\Server\\Event\\SwooleEvent",
        'beforeReload'      => "\\FastD\\Swoole\\Server\\Event\\SwooleEvent",
        'afterReload'       => "\\FastD\\Swoole\\Server\\Event\\SwooleEvent",
        'task'              => "\\FastD\\Swoole\\Server\\Event\\TaskEvent",
        'finish'            => "\\FastD\\Swoole\\Server\\Event\\TaskEvent",
        'request'           => "\\FastD\\Swoole\\Server\\Event\\RequestEvent",
        'beforeHandshakeResponse' => "\\FastD\\Swoole\\Server\\Event\\MessageEvent",
        'handShake'         => "\\FastD\\Swoole\\Server\\Event\\MessageEvent",
        'open'              => "\\FastD\\Swoole\\Server\\Event\\MessageEvent",
        'message'           => "\\FastD\\Swoole\\Server\\Event\\MessageEvent",
        'disconnect'        => "\\FastD\\Swoole\\Server\\Event\\MessageEvent",
        'receive'           => "\\FastD\\Swoole\\Server\\Event\\ReceiveEvent",
        'connect'           => "\\FastD\\Swoole\\Server\\Event\\ReceiveEvent",
        'close'             => "\\FastD\\Swoole\\Server\\Event\\ReceiveEvent",
        'packet'            => "\\FastD\\Swoole\\Server\\Event\\PacketEvent",
    ];

    public function setEvent(string $event, SwooleEvent $swooleEvent): void
    {
        if (!isset($this->serverEvents[$event])) {
            throw new \LogicException("Event $event is not defined.");
        }
        $this->serverEvents[$event] = $swooleEvent;
    }

    public function getEvents(): array
    {
        return $this->serverEvents;
    }

    public function dispatch(object $event): object
    {
        if (
            ($event instanceof StoppableEventInterface && $event->isPropagationStopped())
            || ($event instanceof ThrowableEventInterface && $event->isExceptionStopped())
        ) {
            return $event;
        }

        $listeners = $this->listenerProvider->getListenersForEvent($event);

        foreach ($listeners as $listener) {
            try {
                // 执行监听器
                $listener->process($event);
            } catch (\Throwable $throwable) {
                (new ThrowableListener($listener, $throwable))->process($event);
            }
        }

        return $event;
    }

    public function forward(string $event, array $ports, ...$args): void
    {
        if (isset($this->serverEvents[$event])) {
            $event = new $this->serverEvents[$event]($event, $ports, ...$args);
        } else {
            $event = new SwooleEvent($event, $ports, ...$args);
        }
        $this->dispatch($event);
    }
}