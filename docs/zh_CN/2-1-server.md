# 服务器

实现服务器比较简单，如 Swoole 官网演示一样，仅需要几行代码即可实现一个最简单的服务器。

参数解析: 

```php
Server::createServer($name, $address, array $config = [])

$name 为服务器启动的进程名字
$address 为服务器监听的地址，如: tcp://127.0.0.1:9527, udp://127.0.0.1:9527, ws://127.0.0.1:9527, http://127.0.0.1:9527
$config 服务器配置，保持和 [官网](http://wiki.swoole.com/wiki/page/274.html) 一致，新增 pid_file 选项, 当 pid_file 不存在的时候，默认会存放在当前执行命令的目录下。
```

### TCP Server

```php
use \FastD\Swoole\Server\Tcp;


class DemoServer extends Tcp
{
    public function doWork(swoole_server $server, $fd, $data, $from_id)
    {
        return 'hello tcp';
    }
}

$server = DemoServer::createServer('tcp swoole', 'tcp://0.0.0.0:9527', [
    'pid_file' => '/tmp/swoole.pid',
]);

$server->start();
```

### UDP Server 

```php
use \FastD\Swoole\Server\Udp;


class DemoServer extends Udp
{
    public function doPacket(swoole_server $server, $data, $client_info)
    {
        return 'hello udp';
    }
}

$server = DemoServer::createServer('udp swoole', 'udp://127.0.0.1:9527');

$server->start();
```

### HTTP Server

因为要统一输出，所在在 HTTP Server 中，需要对返回的数据进行统一封装，此处返回使用 [fastd/http](https://github.com/JanHuang/http) 组件进行封装，确保每次返回的数据都是合法的，支持 PSR7。

如果需要调整返回内容或者自定义，则只需要重写父类的 `onRequest` 方法即可。

```php
use \FastD\Swoole\Server\Http;


class Http extends Http
{
    public function doRequest(ServerRequest $serverRequest)
    {
        return new JsonResponse([
            'msg' => 'hello world',
        ]);
    }
}

$server = Http::createServer('http', 'http://0.0.0.0:9527');

$server->start();
```

### WebSocket Server

```php
use \FastD\Swoole\Server\WebSocket;


class WebSocket extends WebSocket
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

WebSocket::createServer('ws', 'ws://0.0.0.0:9527')->start();
```

下一节: [客户端](2-2-client.md)