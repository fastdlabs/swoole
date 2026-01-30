<?php

use FastD\Swoole\Event\Server\RequestEvent;
use FastD\Swoole\Event\SwooleEvent;
use FastD\Swoole\Listener\Server\RequestListener;
use FastD\Swoole\Listener\Server\WorkerListener;
use FastD\Swoole\Listener\SwooleEventListener;

include __DIR__ . '/../../vendor/autoload.php';

//$http = new Http(config, []); // 通过事件调度方式进行调用
// 如果需要自定义worker，manager 等监听，则调用$server->
//$http->listen(127.0.0.1, 8080, Listener);
//$http->start();

class MyListener extends SwooleEventListener
{
    public function process(object $event): void
    {
        echo '执行自定义监听 MyListener, 当前事件类型：' . $event->event . PHP_EOL;
    }

    public function listen(): iterable
    {
        return [
            \FastD\Swoole\Event\Server\ServerEvent::class,
            RequestEvent::class,
        ];
    }
}

class MyRequestListener extends RequestListener
{
    public function onRequest(\Psr\Http\Message\ServerRequestInterface $serverRequest): \Psr\Http\Message\ResponseInterface
    {
        return new \FastD\Http\Response\Text(200, 'hello swoole');
    }
}

$http = new \FastD\Swoole\Server();
// 内部事件，监听，均有 dispatcher 执行，server 只做端口监听方面的操作
//$http->eventDispatcher->listenerProvider->addListener(new \FastD\Swoole\Server\Listener\WorkerListener());
//$http->eventDispatcher->listenerProvider->addListener(new MyListener());
// 监听一个 swoole Server 并且设置事件监听
$http->addListener(new MyListener());
$http->addListener(new WorkerListener());
$http->listen('127.0.0.1', 9527, new MyRequestListener());
$http->start();
