# FastD Swoole

[![Latest Stable Version](https://poser.pugx.org/fastd/swoole/v/stable)](https://packagist.org/packages/fastd/swoole) [![Total Downloads](https://poser.pugx.org/fastd/swoole/downloads)](https://packagist.org/packages/fastd/swoole) [![Latest Unstable Version](https://poser.pugx.org/fastd/swoole/v/unstable)](https://packagist.org/packages/fastd/swoole) [![License](https://poser.pugx.org/fastd/swoole/license)](https://packagist.org/packages/fastd/swoole)

高性能网络服务组件. 提供底层服务封装, 基础管理及客户端调用功能. 使用 `composer` 进行管理, 可在此基础上进行封装整合.

## ＃环境要求

* PHP >= 5.6

* Swoole >= 1.8 (因为 2.0 版本对 PHP7 暂时不够友好,所以暂时不会支持 2.0)

源码地址: [swoole](https://github.com/swoole/swoole-src)

pecl 安装

```shell
pecl install swoole
```

### ＃可选扩展

PHP >= 7.0 的安装 2.0 版本.

源码地址: [inotify](http://pecl.php.net/package/inotify)

pecl 安装

```shell
pecl install inotify
```

### ＃安装

```
composer require "fastd/swoole:1.0.x-dev" -vvv
```

## ＃文档

[中文文档](docs/readme.md)

## ＃使用

服务继承 `FastD\Swoole\Server`, 实现 `doWork` 方法, 服务器在接收信息 `onReceive` 回调中会调用 `doWork` 方法, `doWork` 方法接受一个封装好的请求对象。

具体逻辑在 `doWork` 方法中实现, `doWork` 方法中返回响应客户端的数据, 格式为: **字符串**

服务器通过 `run` 方法执行, `run` 方法中注入配置, 配置按照 `swoole` 原生扩展参数配置。

#### Tcp Server

```php
class DemoServer extends \FastD\Swoole\Server\Tcp
{
    public function doWork(swoole_server $server, $fd, $data, $from_id)
    {
        echo $data . PHP_EOL;
        return 'hello tcp';
    }
}

DemoServer::run('tcp://127.0.0.1:9527');
```

#### Http Server

同理, `Http` 服务器扩展 `Server` 类, 实现 `doRequest` 方法,实现具体逻辑。

```php
class Http extends \FastD\Swoole\Server\Http
{
    public function doRequest(\FastD\Http\SwooleServerRequest $request)
    {
        $request->cookie->set('name', 'jan');

        return new \FastD\Http\JsonResponse([
            'msg' => 'hello world',
        ], 400, [
            'NAME' => "Jan"
        ]);
    }
}

Http::run('http://0.0.0.0:9527');
```

目前 Http 服务支持 Session 存储，而 Session 存储是基于浏览器 cookie，或者可以自定义实现存储方式。

目前由 [FastD/Session](https://github.com/JanHuang/http) 提供 session 支持以及 swoole_http_request 对象解释。

#### WebSocket Server

```php
class WebSocket extends \FastD\Swoole\Server\WebSocket
{
    public function doOpen(swoole_websocket_server $server, swoole_http_request $request)
    {
        echo "server: handshake success with fd{$request->fd}\n";
    }

    public function doMessage(swoole_server $server, swoole_websocket_frame $frame)
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $server->push($frame->fd, "this is server");
    }
}

WebSocket::run('ws://0.0.0.0:9527');
```

#### 服务管理

```php

use FastD\Swoole\Server\Tcp\TcpServer;

class DemoServer extends TcpServer
{
    public function doWork(swoole_server $server, $fd, $data, $from_id)
    {
        return 'hello tcp';
    }
}

$server = new DemoServer('tcp://127.0.0.1:9527');

$argv = $_SERVER['argv'];

$argv[1] = isset($argv[1]) ? $argv[1] : 'status';

switch ($argv[1]) {
    case 'start':
        $server->start();
        break;
    case 'stop':
        $server->shutdown();
        break;
    case 'reload':
        $server->reload();
        break;
    case 'status':
    default:
        $server->status();
}
```

#### File Listener

因为 swoole 是常驻内存，程序通过首次启动就自动预载到内存当中，所以每次修改都需要重启服务，颇为麻烦。

所以这里提供监听文件变化来到自动重启服务(生产环境不建议使用)

```php

```

#### Sync Client

Client 通过 resolve 执行，通过不同的方法设置不同的回调，同步、异步均使用通用的方法。

```php
$client = new \FastD\Swoole\Client\Sync\SyncClient('tcp://11.11.11.11:9527');

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

#### Async Client

```php
$client = new \FastD\Swoole\Client\Async\AsyncClientgit ('tcp://11.11.11.11:9527');

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

# License MIT
