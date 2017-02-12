# 客户端 

首先得必须说明一下什么是客户端，所谓客户端，就是咱们所指的 "消费者"，服务器，也有可能是客户端。可以这么理解，只要是发起请求到另外一段获取获取的，即可视为客户端。因此有时候咱们的服务器，也是其中一个客户端。

客户端最后通过 `resolve` 进行调用。

### 客户端命令行

> 1.1.0 新增

当用户输入 (quit/exit) 的时候，客户端自动退出当前循环。

默认数据为: `Hello World`

```php
$ php swoole [host] [port]
```

效果: 

```
➜  swoole (1.1) ✗ php swoole 11.11.11.22 9527
Please enter the send data.(default: Hello World, Enter (exit/quit) can be exit console.):
Receive: Hello World
Please enter the send data.(default: Hello World, Enter (exit/quit) can be exit console.): exit
```

### 同步客户端

同步客户端是最传统的一种方式，也是最容易上手的，整个过程都是阻塞的。

```php
use FastD\Swoole\Client\Sync\SyncClient;


$client = new SyncClient('tcp://127.0.0.1:9527');

$client
    ->connect(function ($client) {
        $client->send('hello world');
    })
    ->receive(function ($client, $data) {
        echo $data . PHP_EOL;
        $client->close();
    })
    ->resolve()
;
```

### 异步客户端

不管是同步还是异步客户端，每个方法都是一个回调，统一客户端的写法，避免造成多种操作方式的，造成混淆。

值得注意的是，异步客户端需要对每个操作进行回调处理。

```php
use FastD\Swoole\Client\Async\AsyncClient;


$client = new AsyncClient('tcp://127.0.0.1:9527');

$client
    ->connect(function ($client) {
        $client->send('hello world');
    })
    ->receive(function ($client, $data) {
        echo $data . PHP_EOL;
    })
    ->error(function ($client) {
        print_r($client);
    })
    ->close(function ($client) {})
    ->resolve()
;
```

### Socket 客户端

Socket 客户端是基于 PHP 内部的 sockets 扩展实现，因此使用该客户端前先要确保 sockets 扩展已经正常安装。

```php
use FastD\Swoole\Client\Socket;


$socket = new Socket('tcp://127.0.0.1:9527');

$socket->connect(function (Socket $socket) {
    $socket->send('hello world');
})->receive(function ($data) {
    echo ($data) . PHP_EOL;
})->resolve();
```

下一节: [进程](2-3-process.md)