<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/3/7
 * Time: 上午11:50
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Http;

use FastD\Http\SwooleServerRequest;
use FastD\Swoole\Server;
use swoole_http_request;
use swoole_http_response;
use swoole_http_server;
use swoole_server;

/**
 * Class HttpServer
 *
 * @package FastD\Swoole\Server
 */
abstract class HttpServer extends Server
{
    /**
     * @param array $content
     * @return string
     */
    public function json(array $content)
    {
        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $content
     * @return Response
     */
    public function html($content)
    {
        return $content;
    }

    /**
     * @return \swoole_server
     */
    public function initSwoole()
    {
        return new swoole_http_server($this->getHost(), $this->getPort(), $this->mode, $this->sockType);
    }

    /**
     * @param swoole_http_request $swooleRequet
     * @param swoole_http_response $swooleResponse
     */
    public function onRequest(swoole_http_request $swooleRequet, swoole_http_response $swooleResponse)
    {
        try {
            $swooleRequestServer = SwooleServerRequest::createFromSwoole($swooleRequet, $swooleResponse);
            $content = $this->doRequest($swooleRequestServer);
            $swooleResponse->end($content);
        } catch (\Exception $e) {
            $swooleResponse->status(500);
            $swooleResponse->end('Error 500');
        }
    }

    /**
     * @param SwooleServerRequest $request
     * @return mixed
     */
    abstract public function doRequest(SwooleServerRequest $request);

    /**
     * Nothing to do.
     *
     * @param \swoole_server $server
     * @param int $task_id
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    public function doTask(\swoole_server $server, int $task_id, int $from_id, string $data)
    {}

    /**
     * @param swoole_server $server
     * @param $fd
     * @param $data
     * @param $from_id
     * @return mixed
     */
    public function doWork(swoole_server $server, $fd, $data, $from_id)
    {}

    /**
     * @param swoole_server $server
     * @param $data
     * @param $client_info
     * @return mixed
     */
    public function doPacket(swoole_server $server, $data, $client_info)
    {}
}