# FastD Swoole

[![Latest Stable Version](https://poser.pugx.org/fastd/swoole/v/stable)](https://packagist.org/packages/fastd/swoole) [![Total Downloads](https://poser.pugx.org/fastd/swoole/downloads)](https://packagist.org/packages/fastd/swoole) [![Latest Unstable Version](https://poser.pugx.org/fastd/swoole/v/unstable)](https://packagist.org/packages/fastd/swoole) [![License](https://poser.pugx.org/fastd/swoole/license)](https://packagist.org/packages/fastd/swoole)

高性能网络服务组件. 提供底层服务封装, 基础管理及客户端调用功能. 使用 `composer` 进行管理, 可在此基础上进行封装整合.

## 环境要求

* PHP 7+

* Swoole 1.8+

源码地址: [swoole](https://github.com/swoole/swoole-src)

pecl 安装

```shell
pecl install swoole
```

### 可选扩展

PHP >= 7 的安装 2.0 版本.

源码地址: [inotify](http://pecl.php.net/package/inotify)

pecl 安装

```shell
pecl install inotify
```

### 安装 

```
composer require "fastd/swoole:2.0.x-dev" -vvv
```

## 使用

具体 Demo 请移步到 examples 目录下

### Http Server 

创建一个 Http 服务, 建议配合 Nginx 作为代理服务器,可以更加灵活地处理各种事务.

```php
use FastD\Swoole\Server\HttpServer;
$server = HttpServer::create('0.0.0.0', '9321');
$server->handle(new HttpHandler());
$server->start();
```

启动 Http 服务之后, 就可以通过浏览器访问地址: `127.0.0.1:9321` 进行访问.

具体的请求处理在 `http/handler.php` 文件中的 `onRequest` 方法, 需要继承 `\FastD\Swoole\Handler\HttpHandleAbstract` 进行实现.


### Tcp Server

创建服务, 处理回调, 启动进程

```php
// autoload...
use \FastD\Swoole\Server\Server;
$server = Server::create('0.0.0.0', '9321');
$server->handle(new ServerHandler());
$server->start();
```

在 `FastD\Swoole\Handler\HandlerInterface` 对象中, 如果方法是 `on` 开头的, 会在进程启动的时候自动添加到回调处理当中, 所以请注意命名, 与官方命名保持一致.

每个 `ServerInterface` 都实现一个 `handle` 方法. 如果该方法没有绑定自定义处理函数, 则在进程启动是抛出异常.

在示例代码中, 已经有一个最简单的例子了.

### Tcp Client

```php
// autoload...
use FastD\Swoole\Client\Client;

$client = new Client();

$client->connect(host, port);

$client->send('hello world');

echo $client->receive();

$client->close();
```

成功调用服务, 会显示简单的 `hello world` 字样.

### Simple Json RPC Server

一个简单的 Json RPC 服务(暂用于学习和演示)

```php
// autoload...
include __DIR__ . '/handle.php';
include __DIR__ . '/api/demo.php';

use FastD\Swoole\Server\RpcServer;

$server = RpcServer::create('0.0.0.0', '9501');

$demo = new Demo();

$server->add('/test', [$demo, 'emptyArg']);

$server
    ->handle(new RpcHandler())
    ->start()
;
```

通过 `Rpc::add($name, $callback)` 方法设置 `rpc` 接口, 使用方法如下: 

```php
$server->add('/test', [$demo, 'emptyArg']);
```

和配置路由一样, 主要为了区分不同的操作和方法.

回调支持匿名函数(`function () {}`) 数组 (`[$obj, 'action']`) 静态方法 (`Name::action`)

**注意:** 每个回调方法中,必须返回数组. `return ['name' => 'janhuang'];`

### Simple Json Rpc Client

简单的 rpc 客户端. 与 PRC Server 进行配置.

```php
// autoload...
use FastD\Swoole\Client\RpcClient;

$client = new RpcClient('11.11.11.44', '9501');

$client->call('/test');

print_r($client->receive());

$client->close();
```

客户端在接收到相应数据后,会自动解码成数组,客户端可以直接使用.

**说明:** 本实例主要用于演示和学习,谨慎用于生产环境,不喜勿喷,多谢批评

### Manager

对服务进行管理, 目前又简单的: 重启, 重载, 启动, 关闭, 状态的.

管理程序可以通过构造的时候传入进程 `pid` 进行管理, 也可以空值, 示例化后进行服务绑定管理, 详情看例子: `examples/base/manager.php`

### 监听文件变化

监听文件变化, 可以重启服务, 推荐在开发环境下使用, 不建议在生产环境心下进行监听, 除非特定场景, 非用不可.

监听方法整合到 `Manager` 管理器上, 接受两个参数: 

```php
void public function watch(array $directories, \Closure $callback = null)
```

#### directories

&emsp;&emsp;需要监听的目录, 监听单个,多个都需要使用数组进行传递.
 
#### callback

&emsp;&emsp;回调, 当产生变化时, 则会触发回调, 而回调则是可以自定义的. 若回调为空, 则默认使用 `Server` 自身的回调. 回调这里只接受匿名函数.

# License MIT
