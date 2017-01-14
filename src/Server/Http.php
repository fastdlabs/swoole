<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;

use Exception;
use FastD\Http\HttpException;
use FastD\Http\Response;
use FastD\Http\ServerRequest;
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
abstract class Http extends Server
{
    const GZIP_LEVEL = 2;
    const SERVER_INTERVAL_ERROR = 'Server Interval Error';

    public function Http2($key, $pem)
    {

    }

    public function ssl($key, $pem)
    {

    }

    /**
     * @return \swoole_server
     */
    public function initSwoole()
    {
        return new swoole_http_server($this->getHost(), $this->getPort());
    }

    /**
     * @param swoole_http_request $swooleRequet
     * @param swoole_http_response $swooleResponse
     */
    public function onRequest(swoole_http_request $swooleRequet, swoole_http_response $swooleResponse)
    {
        try {
            $swooleRequestServer = SwooleServerRequest::createServerRequestFromSwoole($swooleRequet);

            $response = $this->doRequest($swooleRequestServer);

            foreach ($response->getHeaders() as $key => $header) {
                $swooleResponse->header($key, $response->getHeaderLine($key));
            }

            foreach ($swooleRequestServer->getCookieParams() as $key => $cookieParam) {
                $swooleResponse->cookie($key, $cookieParam);
            }

            $swooleResponse->status($response->getStatusCode());
            $swooleResponse->end((string) $response->getBody());
            unset($response, $swooleRequestServer, $swooleResponse);
        } catch (HttpException $e) {
            $swooleResponse->status($e->getStatusCode());
            $swooleResponse->end($e->getMessage());
        } catch (Exception $e) {
            $swooleResponse->status(500);
            $swooleResponse->end($e->getMessage());
        }
    }

    /**
     * @param ServerRequest $serverRequest
     * @return Response
     */
    abstract public function doRequest(ServerRequest $serverRequest);
}