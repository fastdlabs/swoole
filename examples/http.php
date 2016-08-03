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
use FastD\Swoole\Http\HttpRequest;

class Http extends HttpServer
{
    /**
     * @param Request $request
     * @return string
     */
    public function doRequest(HttpRequest $request)
    {
        return $this->html($request->getPathInfo());
    }
}

Http::run([
    'log_file' => './fds.log',
    'host' => '0.0.0.0',
]);
