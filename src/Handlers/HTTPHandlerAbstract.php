<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Handlers;


use FastD\Http\ServerRequest;
use FastD\Http\SwooleServerRequest;
use Swoole\Http\Request;
use Swoole\Http\Response;

abstract class HTTPHandlerAbstract extends HandlerAbstract implements HTTPHandlerInterface
{
    /**
     * @param ServerRequest $serverRequest
     * @return \FastD\Http\Response
     */
    abstract public function handleRequest(ServerRequest $serverRequest): \FastD\Http\Response;

    /**
     * @param Request $swooleRequet
     * @param Response $swooleResponse
     */
    public function onRequest(Request $swooleRequet, Response $swooleResponse): void
    {
        $serverRequest = SwooleServerRequest::createServerRequestFromSwoole($swooleRequet);

        output(sprintf("Request: {%s}", (string)$serverRequest->getUri()));

        $response = $this->handleRequest($serverRequest);

        $this->handleResponse($swooleResponse, $response);
    }

    /**
     * @param \Swoole\Http\Response $swooleResponse
     * @param \FastD\Http\Response $response
     */
    protected function sendHeader(Response $swooleResponse, \FastD\Http\Response $response)
    {
        foreach ($response->getHeaders() as $key => $header) {
            $swooleResponse->header($key, $response->getHeaderLine($key));
        }

        foreach ($response->getCookieParams() as $key => $cookieParam) {
            $swooleResponse->cookie($key, $cookieParam);
        }
    }

    /**
     * @param \Swoole\Http\Response $swooleResponse
     * @param Response $response
     * @return void
     */
    protected function handleResponse(Response $swooleResponse, \FastD\Http\Response $response): void
    {
        $this->sendHeader($swooleResponse, $response);
        $swooleResponse->status($response->getStatusCode());
        $swooleResponse->end((string) $response->getBody());
    }
}
