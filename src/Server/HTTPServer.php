<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
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
use FastD\Swoole\Handlers\HTTPHandler;
use Swoole\Http\Server;

/**
 * Class HTTPServer
 * @package FastD\Swoole\Server
 */
abstract class HTTPServer extends ServerAbstract
{
    protected string $protocol = 'http';

    protected string $handler = HTTPHandler::class;

    /**
     * @return \Swoole\Server
     */
    public function initSwoole(): \Swoole\Server
    {
        return new Server($this->host, $this->port);
    }

    /**
     * 开启 http2 需要 ssl配置
     * @param string $key
     * @param string $cert
     * @return HTTPServer
     */
    public function enableHTTP2(string $key, string $cert): HTTPServer
    {
        $this->config['open_http2_protocol'] = true;
        $this->config['ssl_cert_file'] = $cert;
        $this->config['ssl_key_file'] = $key;

        return $this;
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
     * @param ServerRequest $request
     * @return Response
     */
    abstract public function handleRequest(ServerRequest $request): Response;

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
