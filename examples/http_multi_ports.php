<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/1/18
 * Time: 下午9:47
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

include __DIR__ . '/../vendor/autoload.php';

use FastD\Swoole\Http\HttpServer;
use FastD\Http\Swoole\SwooleRequest;

class Http extends HttpServer
{
    /**
     * @param SwooleRequest $request
     * @return \FastD\Http\Response
     */
    public function doRequest(SwooleRequest $request)
    {
        $request->setSession('name', 'janhuang');

        return $this->responseJson(['name' => 'jan']);
    }

    public function doWork(\swoole_server $server, int $fd, int $from_id, string $data)
    {
        $server->send($fd, 'hello world');
        $server->close($fd);
    }
}

Http::run([
    'host' => '0.0.0.0',
    'ports' => [
        [
            'host' => '0.0.0.0',
            'port' => '9988',
            'sock' => SWOOLE_SOCK_TCP,
            'config' => [], // 重写端口配置
        ],
    ]
]);
