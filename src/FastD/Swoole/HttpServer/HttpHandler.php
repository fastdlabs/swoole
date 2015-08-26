<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/16
 * Time: 下午7:33
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\HttpServer;

use FastD\Swoole\Server\HttpHandlerAbstract;

class HttpHandler extends HttpHandlerAbstract
{
    /**
     * @param \swoole_http_request  $request
     * @param \swoole_http_response $response
     * @return mixed
     */
    public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
    {
        $response->end('hello swoole');
    }
}