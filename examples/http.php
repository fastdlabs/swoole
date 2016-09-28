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

use FastD\Http\Response;
use FastD\Swoole\Http\HttpServer;

class Http extends HttpServer
{
    /**
     * @param \FastD\Http\SwooleServerRequest $request
     * @return mixed
     */
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

Http::run([
    'log_file' => './fds.log',
    'host' => '0.0.0.0',
]);
