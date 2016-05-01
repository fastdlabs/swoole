<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/3/7
 * Time: 下午12:09
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Handler;

/**
 * Class HttpHandleAbstract
 *
 * @package FastD\Swoole\Handler
 */
abstract class HttpHandleAbstract extends ServerHandlerAbstract
{
    /**
     * @param \swoole_http_request $request
     * @param \swoole_http_response $response
     * @return void
     */
    abstract public function onRequest(\swoole_http_request $request, \swoole_http_response $response);

    /**
     * @param \swoole_server $server
     * @param $fd
     * @param $from_id
     * @param $data
     * @return mixed
     */
    public function onReceive(\swoole_server $server, $fd, $from_id, $data){}

    /**
     * @param \swoole_server $server
     * @param $data
     * @param array $client_info
     * @return mixed
     */
    public function onPacket(\swoole_server $server, $data, array $client_info){}
}