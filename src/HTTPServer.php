<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;

use Exception;
use FastD\Http\HttpException;
use FastD\Http\Request;
use FastD\Http\Response;
use FastD\Http\SwooleServerRequest;
use Swoole\Http\Server;
use FastD\Swoole\Handlers\HTTPServerHandlerInterface;

/**
 * Class HTTPServer
 * @package FastD\Swoole\Server
 */
abstract class HTTPServer extends ServerAbstract implements HTTPServerHandlerInterface
{
    protected $protocol = 'http';

    const SERVER_INTERVAL_ERROR = 'Server Interval Error';

    /**
     * @return \swoole_http_server
     */
    public function initSwoole(): \Swoole\Server
    {
        return new Server($this->getHost(), $this->getPort());
    }

    /**
     * @return HTTPServer
     */
    public function enableHTTP2(): HTTPServer
    {
        $this->config['open_http2_protocol'] = true;

        return $this;
    }

    /**
     * @param swoole_http_request $swooleRequet
     * @param swoole_http_response $swooleResponse
     */
    public function onRequest(\Swoole\Http\Request $swooleRequet, \Swoole\Http\Response $swooleResponse): void
    {
        try {
            $swooleRequestServer = SwooleServerRequest::createServerRequestFromSwoole($swooleRequet);
            $response = $this->handleRequest($swooleRequestServer);
            $this->handleResponse($swooleResponse, $response);
            unset($response);
        } catch (HttpException $e) {
            $swooleResponse->status($e->getStatusCode());
            $swooleResponse->end($e->getMessage());
        } catch (Exception $e) {
            $swooleResponse->status(500);
            $swooleResponse->end(static::SERVER_INTERVAL_ERROR);
        }
    }

    /**
     * @param swoole_http_response $swooleResponse
     * @param Response $response
     */
    protected function sendHeader(swoole_http_response $swooleResponse, Response $response)
    {
        foreach ($response->getHeaders() as $key => $header) {
            $swooleResponse->header($key, $response->getHeaderLine($key));
        }

        foreach ($response->getCookieParams() as $key => $cookieParam) {
            $swooleResponse->cookie($key, $cookieParam);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    abstract public function handleRequest(Request $request): Response;

    /**
     * @param swoole_http_response $swooleResponse
     * @param Response $response
     * @return void
     */
    public function handleResponse(\Swoole\Http\Response $swooleResponse, Response $response): void
    {
        $this->sendHeader($swooleResponse, $response);
        $swooleResponse->status($response->getStatusCode());
        $swooleResponse->end((string) $response->getBody());
    }
}