# FastD Swoole

[![Build Status](https://travis-ci.org/fastdlabs/swoole.svg?branch=master)](https://travis-ci.org/fastdlabs/swoole)
[![PHP Require Version](https://img.shields.io/badge/php-%3E%3D7.1-8892BF.svg)](https://secure.php.net/)
[![Swoole Require Version](https://img.shields.io/badge/swoole-%3E%3D4.2.0-8892BF.svg)](http://www.swoole.com/)
[![Latest Stable Version](https://poser.pugx.org/fastd/swoole/v/stable)](https://packagist.org/packages/fastd/swoole)
[![Total Downloads](https://poser.pugx.org/fastd/swoole/downloads)](https://packagist.org/packages/fastd/swoole) 
[![Latest Unstable Version](https://poser.pugx.org/fastd/swoole/v/unstable)](https://packagist.org/packages/fastd/swoole) 
[![License](https://poser.pugx.org/fastd/swoole/license)](https://packagist.org/packages/fastd/swoole)

高性能网络服务组件. 提供底层服务封装, 基础管理及客户端调用功能. 使用 `composer` 进行管理, 可在此基础上进行封装整合.

## 环境要求

* Linux (不考虑windows)
* swoole >= 6.0
* PHP >= 8.2

> 注意：代码使用了 PHP 8.2 的特性（如 readonly 属性）和 Swoole 6.0+ 的 API，确保你的环境满足这些要求。

### 版本兼容性

- PHP 8.2+ 推荐使用最新版本
- Swoole 6.0+ 提供更好的性能和稳定性

源码地址: [swoole](https://github.com/swoole/swoole-src)

pecl 安装

```shell
pecl install swoole
```

### 安装

```
composer require fastd/swoole
```

## 文档

[中文文档](docs/zh_CN/readme.md)

## 使用

FastD Swoole 采用事件驱动架构，使用事件监听器来处理各种服务器事件。以下是使用示例：

### HTTP 服务器

```php
use FastD\Swoole\Server;
use FastD\Swoole\Listener\Server\RequestListener;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use FastD\Http\Response\Json;

// 创建 HTTP 请求监听器
class HttpListener extends RequestListener
{
    public function onRequest(ServerRequestInterface $serverRequest): ResponseInterface
    {
        // 处理 HTTP 请求
        $path = $serverRequest->getUri()->getPath();
        
        return new Json(200, [
            'message' => 'Hello, FastD Swoole!',
            'path' => $path,
            'method' => $serverRequest->getMethod(),
        ]);
    }
}

// 创建服务器实例
$server = new Server([
    'worker_num' => 4,
    'pid_file' => '/tmp/http_server.pid',
    'log_file' => '/tmp/http_server.log',
]);

// 监听 HTTP 端口
$server->listen('0.0.0.0', 9527, new HttpListener(), 'http');

// 启动服务器
$server->start();
```

### TCP 服务器

```php
use FastD\Swoole\Server;
use FastD\Swoole\Listener\SwooleEventListener;
use FastD\Swoole\Event\Server\ReceiveEvent;
use FastD\Swoole\Event\SwooleEvent;

// 创建 TCP 接收监听器
class TcpListener extends SwooleEventListener
{
    public function listen(): iterable
    {
        return [
            ReceiveEvent::class,
        ];
    }
    
    public function process(object $event): void
    {
        if ($event instanceof SwooleEvent && $event->event == 'receive') {
            [$server, $fd, $fromId, $data] = $event->args;
            
            // 处理接收到的数据
            echo "Received: {$data}" . PHP_EOL;
            
            // 发送响应
            $server->send($fd, "Echo: {$data}");
        }
    }
}

// 创建服务器实例
$server = new Server([
    'worker_num' => 2,
    'pid_file' => '/tmp/tcp_server.pid',
]);

// 监听 TCP 端口
$server->listen('0.0.0.0', 9528, new TcpListener(), 'tcp');

// 启动服务器
$server->start();
```

### WebSocket 服务器

```php
use FastD\Swoole\Server;
use FastD\Swoole\Listener\SwooleEventListener;
use FastD\Swoole\Event\SwooleEvent;

// 创建 WebSocket 监听器
class WsListener extends SwooleEventListener
{
    public function listen(): iterable
    {
        return [
            'open',
            'message',
            'close',
        ];
    }
    
    public function process(object $event): void
    {
        if ($event instanceof SwooleEvent) {
            switch ($event->event) {
                case 'open':
                    [$server, $request] = $event->args;
                    echo "WebSocket connected: {$request->fd}" . PHP_EOL;
                    break;
                    
                case 'message':
                    [$server, $frame] = $event->args;
                    echo "WebSocket message from {$frame->fd}: {$frame->data}" . PHP_EOL;
                    // 广播消息
                    $server->push($frame->fd, "Server: {$frame->data}");
                    break;
                    
                case 'close':
                    [$server, $fd] = $event->args;
                    echo "WebSocket disconnected: {$fd}" . PHP_EOL;
                    break;
            }
        }
    }
}

// 创建服务器实例
$server = new Server([
    'worker_num' => 2,
    'pid_file' => '/tmp/ws_server.pid',
]);

// 监听 WebSocket 端口
$server->listen('0.0.0.0', 9529, new WsListener(), 'ws');

// 启动服务器
$server->start();
```

### 多端口支持

```php
use FastD\Swoole\Server;
use FastD\Swoole\Listener\Server\RequestListener;
use FastD\Swoole\Listener\SwooleEventListener;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use FastD\Http\Response\Json;

// HTTP 监听器
class HttpListener extends RequestListener
{
    public function onRequest(ServerRequestInterface $serverRequest): ResponseInterface
    {
        return new Json(200, ['message' => 'HTTP Server']);
    }
}

// TCP 监听器
class TcpListener extends SwooleEventListener
{
    public function listen(): iterable
    {
        return ['receive'];
    }
    
    public function process(object $event): void
    {
        if ($event->event == 'receive') {
            [$server, $fd, $fromId, $data] = $event->args;
            $server->send($fd, "TCP Server: {$data}");
        }
    }
}

// 创建服务器实例
$server = new Server([
    'worker_num' => 4,
    'pid_file' => '/tmp/multi_port_server.pid',
]);

// 监听 HTTP 端口
$server->listen('0.0.0.0', 9527, new HttpListener(), 'http');

// 监听 TCP 端口
$server->listen('0.0.0.0', 9528, new TcpListener(), 'tcp');

// 启动服务器
$server->start();
```

### 服务管理

```php
use FastD\Swoole\Server;
use FastD\Swoole\Listener\Server\RequestListener;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use FastD\Http\Response\Json;

class HttpListener extends RequestListener
{
    public function onRequest(ServerRequestInterface $serverRequest): ResponseInterface
    {
        return new Json(200, ['message' => 'Hello, FastD Swoole!']);
    }
}

$server = new Server([
    'worker_num' => 4,
    'pid_file' => '/tmp/http_server.pid',
]);

$server->listen('0.0.0.0', 9527, new HttpListener(), 'http');

// 处理命令行参数
$action = $argv[1] ?? 'start';

switch ($action) {
    case 'start':
        echo "Starting server...\n";
        $server->start();
        break;
    case 'stop':
        echo "Stopping server...\n";
        $server->stop();
        break;
    case 'reload':
        echo "Reloading server...\n";
        $server->reload();
        break;
    case 'status':
        $status = $server->status() ? 'running' : 'stopped';
        echo "Server status: {$status}\n";
        break;
    default:
        echo "Unknown action: {$action}\n";
        echo "Usage: php server.php [start|stop|reload|status]\n";
        break;
}
```



#### Process

```php
use FastD\Swoole\Process;
use FastD\Swoole\Process\Worker;

// 创建一个工作进程
class MyWorker extends Worker
{
    public function __construct()
    {
        parent::__construct('my-worker');
    }
    
    public function process(Worker $worker): void
    {
        \Swoole\Timer::tick(1000, function ($id) {
            static $index = 0;
            $index++;
            echo $index . PHP_EOL;
            if ($index === 10) {
                \Swoole\Timer::clear($id);
            }
        });
        
        // 保持进程运行
        \Swoole\Event::wait();
    }
}

// 创建进程管理器
$process = new Process();

// 添加工作进程
$process->addWorker(new MyWorker());

// 启动进程管理器
$process->start();
```

#### Multi Process

```php
use FastD\Swoole\Process;
use FastD\Swoole\Process\Worker;

// 创建一个工作进程类
class MyWorker extends Worker
{
    public function __construct(string $name)
    {
        parent::__construct($name);
    }
    
    public function process(Worker $worker): void
    {
        echo "Worker {$worker->name} started with PID: " . $worker->getPid() . PHP_EOL;
        
        // 模拟工作
        \Swoole\Timer::tick(1000, function ($id) use ($worker) {
            static $index = 0;
            $index++;
            echo "Worker {$worker->name}: {$index}" . PHP_EOL;
            if ($index === 5) {
                \Swoole\Timer::clear($id);
            }
        });
        
        // 保持进程运行
        \Swoole\Event::wait();
    }
}

// 创建进程管理器
$process = new Process();

// 添加多个工作进程
for ($i = 1; $i <= 5; $i++) {
    $process->addWorker(new MyWorker("worker-{$i}"));
}

// 启动进程管理器
$process->start();
```



#### IPC (Inter-Process Communication)

FastD Swoole 提供了三种 IPC 实现，用于进程间通信：

1. **Memory**: 基于 Swoole\Table 的共享内存实现
2. **Queue**: 基于 Swoole\Process\useQueue 的消息队列实现
3. **Socket**: 基于 Swoole\Coroutine\Socket 的套接字实现

##### IPC 使用示例

```php
// 基本 IPC 使用示例
// 参考 examples/process/ipc_example.php

// 多进程 IPC 管理示例
// 参考 examples/process/ipc_process.php
```

##### 与 Worker 集成

```php
use FastD\Swoole\Process\Worker;
use FastD\Swoole\Process\IPC\Memory;
use FastD\Swoole\Process\IPC\Queue;
use FastD\Swoole\Process\IPC\Socket;
use FastD\Swoole\Process\IPC\IPC;

// 创建带 Memory IPC 的 Worker
$memoryWorker = new Worker('memory-worker', function (Worker $worker) {
    $ipc = $worker->getIPC();
    $ipc->write('hello from memory');
    $data = $ipc->read();
    echo $data . PHP_EOL;
}, new Memory());

// 创建带 Queue IPC 的 Worker
$queueWorker = new Worker('queue-worker', function (Worker $worker) {
    $ipc = $worker->getIPC();
    $ipc->write('hello from queue');
    $data = $ipc->read();
    echo $data . PHP_EOL;
}, new Queue());

// 创建带 Socket IPC 的 Worker
$socketWorker = new Worker('socket-worker', function (Worker $worker) {
    $ipc = $worker->getIPC();
    $ipc->write('hello from socket');
    $data = $ipc->read();
    echo $data . PHP_EOL;
}, new Socket());

// 启动 Worker
$memoryWorker->start();
$queueWorker->start();
$socketWorker->start();

// 等待 Worker 结束
$memoryWorker->wait(function ($ret) {
    echo 'PID: ' . $ret['pid'] . PHP_EOL;
});

$queueWorker->wait(function ($ret) {
    echo 'PID: ' . $ret['pid'] . PHP_EOL;
});

$socketWorker->wait(function ($ret) {
    echo 'PID: ' . $ret['pid'] . PHP_EOL;
});
```

### 贡献

非常欢迎感兴趣，愿意参与其中，共同打造更好PHP生态，Swoole生态的开发者。

如果你乐于此，却又不知如何开始，可以试试下面这些事情：

* 在你的系统中使用，将遇到的问题 [反馈](https://github.com/JanHuang/fastD/issues)。
* 有更好的建议？欢迎联系 [bboyjanhuang@gmail.com](mailto:bboyjanhuang@gmail.com) 或 [新浪微博:编码侠](http://weibo.com/ecbboyjan)。

### 联系

如果你在使用中遇到问题，请联系: [bboyjanhuang@gmail.com](mailto:bboyjanhuang@gmail.com). 微博: [编码侠](http://weibo.com/ecbboyjan)

## License MIT
