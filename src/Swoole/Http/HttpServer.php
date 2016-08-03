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

use FastD\Swoole\Server;
use FastD\Swoole\Request;
use FastD\Swoole\Response;

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
        return new \swoole_http_server($this->getHost(), $this->getPort(), $this->mode, $this->sockType);
    }

    /**
     * @param \swoole_http_request $swooleRequet
     * @param \swoole_http_response $swooleResponse
     */
    public function onRequest(\swoole_http_request $swooleRequet, \swoole_http_response $swooleResponse)
    {
        try {
            $request = new HttpRequest($swooleRequet);
            $content = $this->doRequest($request);
            $response = new HttpResponse($swooleResponse, $content);
            if ($request->session->isHit()) {
                $request->cookie[HttpSession::TOKEN] = $request->session->getSessionId();
            }
            $response->setCookies($request->cookie);
            $response->setHeaders($request->headers);
        } catch (\Exception $e) {
            $response = new HttpResponse($swooleResponse, 'Error 500');
            $response->setStatus(500);
        }
        $response->send();
        unset($request, $response);
    }

    /**
     * @param HttpRequest $request
     * @return string
     */
    abstract public function doRequest(HttpRequest $request);

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
    {
        return;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function doWork(Request $request){}

    /**
     * @param Request $request
     * @return Response
     */
    public function doPacket(Request $request){}
}