# FastD Swoole

[![Latest Stable Version](https://poser.pugx.org/fastd/swoole/v/stable)](https://packagist.org/packages/fastd/swoole) [![Total Downloads](https://poser.pugx.org/fastd/swoole/downloads)](https://packagist.org/packages/fastd/swoole) [![Latest Unstable Version](https://poser.pugx.org/fastd/swoole/v/unstable)](https://packagist.org/packages/fastd/swoole) [![License](https://poser.pugx.org/fastd/swoole/license)](https://packagist.org/packages/fastd/swoole)

高性能网络服务组件. 提供底层服务封装, 基础管理及客户端调用功能. 使用 `composer` 进行管理, 可在此基础上进行封装整合.

## ＃环境要求

* PHP 7+

* Swoole 1.8+

源码地址: [swoole](https://github.com/swoole/swoole-src)

pecl 安装

```shell
pecl install swoole
```

### ＃可选扩展

PHP >= 7 的安装 2.0 版本.

源码地址: [inotify](http://pecl.php.net/package/inotify)

pecl 安装

```shell
pecl install inotify
```

### ＃安装

```
{
    "fastd/swoole": "2.0.x-dev"
}
```

## ＃使用

组件提供处理脚本，用于检测配置环境和安装骨架环境。

```php
php install check
```

检测服务器环境配置。安装 `fastd/swoole` 配置。

```php
php install
```

脚本自动生成 `etc/server.ini` 配置文件，服务在运行时会自动读取配置文件配置信息。

```php
use FastD\Swoole\Server\TcpServer;
use FastD\Swoole\Console\Service;

$server = TcpServer::create();

$server->on('receive', function (\swoole_server $server, $fd) {
    echo 'receive' . PHP_EOL;
    $server->close($fd);
});

$server->start();
```

在服务 `create`，也就是实例化的时候加载系统配置文件内容。

也可以在构造方法中注入服务基础配置信息: `Server::__construct($host, $port, $mode, $sock_type)`

使用 `on` 方法对服务方法绑定回调处理。

### ＃Examples

示例目录: `examples`

```php
php examples/base/server.php start
```

服务管理脚本:

```php
php examples/base/server.php {status|stop|reload}
```

##### ＃开启 monitor 监控端口

```php
php examples/base/server_monitor.php start
```

开启 monitor 监听端口默认端口是 9599，IP 为本地IP，**仅供**内网管理。

```php
php examples/base/server_monitor.php {status|stop|reload}
```

##### ＃Server 服务端

提供 TCP 基础服务，演示请看: [server.php](examples/base/server.php)

实现自: `FastD\Swoole\Server\ServerInterface` 继承自: `FastD\Swoole\Server\Server` 抽象类

实现 `initServer` 抽象方法，返回 `\swoole_server` 实例。

```php
/**
 * @return \swoole_server
 */
public function initSwooleServer()
{
    return new \swoole_server($this->getHost(), $this->getPort(), $this->getMode(), $this->getSock());
}
```

##### ＃Client 客户端

`Client` 客户端继承 `\swoole_client`，因此在使用上没有差别。

```php
use FastD\Swoole\Client\Client;

$client = new Client();

$client->connect($host, $port);

$client->send('hello world');

echo $client->receive();

$client->close();
```

##### ＃Handle 事件回调处理

服务器，客户端均可以设置回调处理，客户端(`Client`)因为是继承 `\swoole_cilent` 因此操作方法上没有差别，而服务端是在 `\swoole_server` 在扩展了一层，新增一个处理方法 `handle(\FastD\Swoole\Handler\HandlerAbstract $handle)`。

方法中会调用服务中的 `on` 方法，而设置的 `handle` 对象则会将所有以 `on` 开头的方法进行绑定。如下：

```php
$server = TcpServer::create();

$server->handle(new \FastD\Swoole\Handler\Handle());
```

自动解析类方法并且绑定回调处理。

##### ＃Watcher 开发环境配置



# License MIT
