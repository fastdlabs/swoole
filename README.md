#Fast D Swoole

高性能网络服务组件. 提供底层服务封装, 基础管理及客户端调用功能. 使用 `composer` 进行管理, 可在此基础上进行封装整合.

## 环境要求

Swoole >= 1.8.0 
PHP >= 5.6

源码地址: [swoole](https://github.com/swoole/swoole-src)

pecl 安装

```shell
pecl install swoole
```

### 可选扩展

PHP >= 7 的安装 2.0 版本, 其他的安装 2.0 一下的即可.

源码地址: [inotify](http://pecl.php.net/package/inotify)

pecl 安装

```shell
pecl install inotify
```

## 使用

### Server

创建服务, 处理回调, 启动进程

```php
// code...
use \FastD\Swoole\Server\Server;
$server = new Server(host, port);
// 或者 两者等价
// $server = Server::create('0.0.0.0', '9321');
// $server->handle(); // 处理回调, 传入 FastD\Swoole\Handler\HandlerInterface 对象
$server->start(); 
// 或者使用管理器进行启动, 两者是等价的.
// use FastD\Swoole\Manager\ServerManager;
// $manager = new ServerManager();
// $manager->bindServer($server);
// $manager->start();
```

在 `FastD\Swoole\Handler\HandlerInterface` 对象中, 如果方法是 `on` 开头的, 会在进程启动的时候自动添加到回调处理当中, 所以请注意命名, 与官方命名保持一致.

每个 `ServerInterface` 都实现一个 `handle` 方法. 如果该方法没有绑定自定义处理函数, 则在进程启动是抛出异常.

在示例代码中, 已经有一个最简单的例子了.

### Client

```php
// code...
use FastD\Swoole\Client\Client;

$client = new Client();

$client->connect(host, port);

$client->send('hello world');

echo $client->receive();

$client->close();
```

成功调用服务, 会显示简单的 `hello world` 字样.

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