<?php

declare(strict_types=1);

namespace FastD\Swoole;

use FastD\Event\EventDispatcher;
use FastD\Event\ThrowableEventInterface;
use FastD\Event\ThrowableListener;
use FastD\Swoole\Event\SwooleEvent;
use Psr\EventDispatcher\StoppableEventInterface;

class SwooleEventDispatcher extends EventDispatcher
{
    protected array $events = [
        // server 事件
        'start'             => "\\FastD\\Swoole\\Event\\Server\\ServerEvent",
        'beforeShutdown'    => "\\FastD\\Swoole\\Event\\Server\\ServerEvent",
        'shutdown'          => "\\FastD\\Swoole\\Event\\Server\\ServerEvent",
        'workerStart'       => "\\FastD\\Swoole\\Event\\Server\\ServerEvent",
        'workerStop'        => "\\FastD\\Swoole\\Event\\Server\\ServerEvent",
        'workerError'       => "\\FastD\\Swoole\\Event\\Server\\ServerEvent",
        'workerExit'        => "\\FastD\\Swoole\\Event\\Server\\ServerEvent",
        'pipeMessage'       => "\\FastD\\Swoole\\Event\\Server\\ServerEvent",
        'managerStart'      => "\\FastD\\Swoole\\Event\\Server\\ServerEvent",
        'managerStop'       => "\\FastD\\Swoole\\Event\\Server\\ServerEvent",
        'beforeReload'      => "\\FastD\\Swoole\\Event\\Server\\ServerEvent",
        'afterReload'       => "\\FastD\\Swoole\\Event\\Server\\ServerEvent",
        'task'              => "\\FastD\\Swoole\\Event\\Server\\TaskEvent",
        'finish'            => "\\FastD\\Swoole\\Event\\Server\\TaskEvent",
        'request'           => "\\FastD\\Swoole\\Event\\Server\\RequestEvent",
        'beforeHandshakeResponse' => "\\FastD\\Swoole\\Event\\Server\\MessageEvent",
        'handShake'         => "\\FastD\\Swoole\\Event\\Server\\MessageEvent",
        'open'              => "\\FastD\\Swoole\\Event\\Server\\MessageEvent",
        'message'           => "\\FastD\\Swoole\\Event\\Server\\MessageEvent",
        'disconnect'        => "\\FastD\\Swoole\\Event\\Server\\MessageEvent",
        'receive'           => "\\FastD\\Swoole\\Event\\Server\\ReceiveEvent",
        'connect'           => "\\FastD\\Swoole\\Event\\Server\\ReceiveEvent",
        'close'             => "\\FastD\\Swoole\\Event\\Server\\ReceiveEvent",
        'packet'            => "\\FastD\\Swoole\\Event\\Server\\PacketEvent",
        // 进程事件
        'SIGPIPE'           => '\\FastD\\Swoole\\Event\\Process\\SignalEvent', // 管道破裂/套接字连接关闭；监听网络/管道通信异常，避免进程意外退出
        'SIGINT'            => '\\FastD\\Swoole\\Event\\Process\\QuitEvent', // Crtl+c
        'SIGTERM'           => '\\FastD\\Swoole\\Event\\Process\\QuitEvent', // kill命令默认信号；生产环境进程优雅终止核心信号，清理资源+安全回收子进程
        'SIGCHLD'           => '\\FastD\\Swoole\\Event\\Process\\QuitEvent', // Swoole Process核心信号；子进程正常/异常退出时触发，非阻塞回收子进程资源，避免僵尸进程
        'SIGUSR1'           => '\\FastD\\Swoole\\Event\\Process\\UserEvent', // 自定义转发事件
    ];

    /**
     * 可以通过该方法重设事件
     *
     * @param string $event
     * @param SwooleEvent $swooleEvent
     * @return void
     */
    public function setEvent(string $event, SwooleEvent $swooleEvent): void
    {
        if (!isset($this->events[$event])) {
            throw new \LogicException("Event $event is not defined.");
        }
        $this->events[$event] = $swooleEvent;
    }

    public function getEvents(): array
    {
        return $this->events;
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

    /**
     * 事件转发
     *
     * @param string $event
     * @param object $object
     * @param ...$args
     * @return void
     */
    public function forward(string $event, object $object, ...$args): void
    {
        if (isset($this->events[$event])) {
            $event = new $this->events[$event]($event, $object, ...$args);
        } else {
            $event = new SwooleEvent($event, $object, ...$args);
        }
        $this->dispatch($event);
    }
}