<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/16
 * Time: 下午7:15
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Server;

abstract class HttpHandler extends ServerHandler
{
    /**
     * @param \swoole_http_request  $request
     * @param \swoole_http_response $response
     * @return mixed
     */
    abstract public function onRequest(\swoole_http_request $request, \swoole_http_response $response);
}