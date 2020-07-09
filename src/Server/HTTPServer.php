<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
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
abstract class HTTPServer extends ServerAbstract
{
    protected string $protocol = 'http';

    /**
     * @return \Swoole\Server
     */
    public function initSwoole(): \Swoole\Server
    {
        return new Server($this->getHost(), $this->getPort());
    }

    /**
     * 开启 http2 需要 ssl配置
     */
    public function enableHTTP2(string $key, string $rem): HTTPServer
    {
        $this->config['open_http2_protocol'] = true;

        return $this;
    }

    /**
     * @param \Swoole\Http\Request $swooleRequet
     * @param \Swoole\Http\Response $swooleResponse
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
            $swooleResponse->status(Response::HTTP_INTERNAL_SERVER_ERROR);
            $swooleResponse->end(Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR]);
        }
    }

    /**
     * @param \Swoole\Http\Response $swooleResponse
     * @param Response $response
     */
    protected function sendHeader(\Swoole\Http\Response $swooleResponse, Response $response)
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
     * @param \Swoole\Http\Response $swooleResponse
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
