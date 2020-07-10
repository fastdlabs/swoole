<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Handlers;


use FastD\Http\HttpException;
use FastD\Http\SwooleServerRequest;
use Swoole\Http\Request;
use Swoole\Http\Response;

abstract class HTTPHandler implements HandlerInterface
{
    /**
     * @param \Swoole\Http\Request $swooleRequet
     * @param \Swoole\Http\Response $swooleResponse
     */
    public function onRequest(\Swoole\Http\Request $swooleRequest, \Swoole\Http\Response $swooleResponse): void
    {
        output(sprintf("request: <info>%s</info>", $swooleRequest->server['path_info']));

        try {
            $swooleRequestServer = SwooleServerRequest::createServerRequestFromSwoole($swooleRequest);
            $response = $this->handleRequest($swooleRequestServer);
            $this->handleResponse($swooleResponse, $response);
            unset($response);
        } catch (HttpException $e) {
            $swooleResponse->status($e->getStatusCode());
            $swooleResponse->end($e->getMessage());
        } catch (Exception $e) {
            $swooleResponse->status(\FastD\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
            $swooleResponse->end(Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR]);
        }
    }
}
