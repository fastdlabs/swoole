<?php

use FastD\Swoole\Event\Process\UserEvent;
use FastD\Swoole\Listener\SwooleEventListener;

include __DIR__ . '/../../vendor/autoload.php';

class MyListener extends SwooleEventListener
{
    public function process(object $event): void
    {
        echo '执行自定义监听 MyListener, 当前事件类型：' . $event->event . PHP_EOL;
    }

    public function listen(): iterable
    {
        return [
            UserEvent::class,
        ];
    }
}

$http = new \FastD\Swoole\Server();
$http->addListener(new MyListener());
var_dump($http->status());
