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
use FastD\Swoole\Request;
use FastD\Swoole\Http\HttpRequest;

/**
 * Class Http
 */
class Http extends HttpServer
{
    /**
     * @param HttpRequest $request
     * @return string
     */
    public function doRequest(HttpRequest $request)
    {
        return $this->json([
            'name' => 'jan'
        ]);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function doWork(Request $request)
    {
        return 'hello http port';
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
