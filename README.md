# FastD Swoole

[![Latest Stable Version](https://poser.pugx.org/fastd/swoole/v/stable)](https://packagist.org/packages/fastd/swoole) [![Total Downloads](https://poser.pugx.org/fastd/swoole/downloads)](https://packagist.org/packages/fastd/swoole) [![Latest Unstable Version](https://poser.pugx.org/fastd/swoole/v/unstable)](https://packagist.org/packages/fastd/swoole) [![License](https://poser.pugx.org/fastd/swoole/license)](https://packagist.org/packages/fastd/swoole)

高性能网络服务组件. 提供底层服务封装, 基础管理及客户端调用功能. 使用 `composer` 进行管理, 可在此基础上进行封装整合.

## ＃环境要求

* PHP 7.0+

* Swoole 1.8+ (期待2.0)

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

## ＃使用

服务继承 `FastD\Swoole\Server`, 实现 `doWork` 方法, 服务器在接收信息 `onReceive` 回调中会调用 `doWork` 方法, `doWork` 方法接受一个封装好的请求对象。

具体逻辑在 `doWork` 方法中实现, `doWork` 方法中返回响应客户端的数据, 格式为: **字符串**

服务器通过 `run` 方法执行, `run` 方法中注入配置, 配置按照 `swoole` 原生扩展参数配置。

配置扩展了几个常用参数.

```php
return [
    'pid' => 'pid 文件目录地址', // 选填, 默认当前目录下的 run 目录, run 目录会自动创建
    'host' => '机器ip',
    'port' => '机器端口',
    'mode' => '服务模式,参考官网',
    'sock' => 'sock类型,参考官网',
];
```

```php
use FastD\Swoole\Server;

class DemoServer extends Server
{
    /**
     * @param \FastD\Swoole\Request $request
     * @return string
     */
    public function doWork(\FastD\Swoole\Request $request)
    {
        return $request->getData();
    }

    /**
     * @param \FastD\Swoole\Request $request
     * @return string
     */
    public function doPacket(\FastD\Swoole\Request $request)
    {
        // UDP Receive
    }
}

DemoServer::run([]);
```

同理, `Http` 服务器扩展 `Server` 类, 实现 `doRequest` 方法,实现具体逻辑。

```php
use FastD\Swoole\Http\HttpServer;

class Http extends HttpServer
{
    /**
     * @param \FastD\Swoole\Request $request
     * @return \FastD\Swoole\Response
     */
    public function doRequest(\FastD\Swoole\Http\HttpRequest $request)
    {
        return $this->html('hello world');
    }
}

Http::run([]);
```

服务 `Service` 管理, 修改服务 `Service` 管理, 可以通过注入服务, 对其进行 `{start|status|stop|reload}` 等操作管理。

```php
use FastD\Swoole\Server;
use FastD\Swoole\Console\Service;

class Demo extends Server
{
    /**
     * @param \FastD\Swoole\Request $request
     * @return string
     */
    public function doWork(\FastD\Swoole\Request $request)
    {
        return 'hello service';
    }

    /**
     * @param \FastD\Swoole\Request $request
     * @return string
     */
    public function doPacket(\FastD\Swoole\Request $request)
    {
        // TODO: Implement doPacket() method.
    }
}

$service = Service::server(Demo::class, [

]);

$action = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'status';

switch ($action) {
    case 'status':
        $service->status();
        break;
    case 'start':
        $service->start();
        break;
    case 'stop':
        $service->shutdown();
        break;
    case 'reload':
        $service->reload();
        break;
    case 'watch':
        $service->watch(['./watch']);
        break;
}
```

`Service` 通过 `server($server, array $config)` 注入服务, 实现管理。

Service 提供文件监听功能, 通过监听文件实现自动重启服务。

上述 `watch` 方法中, watch 方法监听多个目录, 若监听目录中, 文件发生变化, 服务会自动重启, 推荐在开发环境下使用。

**watch 依赖 php inotify 扩展。**

# License MIT
