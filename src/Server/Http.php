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
use FastD\Http\Exceptions\HttpException;
use FastD\Http\JsonResponse;
use FastD\Http\Response;
use FastD\Http\SwooleServerRequest;
use FastD\Session\Session;
use FastD\Swoole\Exceptions\CannotResponseException;
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
    const SERVER_INTERVAL_ERROR = 'Server Interval';

    /**
     * @param array $content
     * @return Response
     */
    public function json(array $content)
    {
        return new JsonResponse($content);
    }

    /**
     * @param $content
     * @return Response
     */
    public function html($content)
    {
        return new Response($content);
    }

    /**
     * @return \swoole_server
     */
    public function initSwoole()
    {
        return new swoole_http_server($this->getHost(), $this->getPort(), $this->mode);
    }

    /**
     * @param swoole_http_request $swooleRequet
     * @param swoole_http_response $swooleResponse
     */
    public function onRequest(swoole_http_request $swooleRequet, swoole_http_response $swooleResponse)
    {
        try {
            $swooleRequestServer = SwooleServerRequest::createFromSwoole($swooleRequet, $swooleResponse);

            if (!(($response = $this->doRequest($swooleRequestServer)) instanceof Response)) {
                throw new CannotResponseException();
            }

            $swooleResponse->status($response->getStatusCode());

            if (!empty($sessionId = $swooleRequestServer->session->getSessionId())) {
                $swooleResponse->header(Session::SESSION_KEY, $sessionId);
            }

            foreach ($response->getHeaders() as $key => $header) {
                $swooleResponse->header($key, $response->getHeaderLine($key));
            }

            foreach ($swooleRequestServer->getCookieParams() as $cookieParam) {
                $swooleResponse->cookie(
                    $cookieParam->getName(),
                    $cookieParam->getValue(),
                    $cookieParam->getExpire(),
                    $cookieParam->getPath(),
                    $cookieParam->getDomain(),
                    $cookieParam->isSecure(),
                    $cookieParam->isHttpOnly()
                );
            }
            $swooleResponse->gzip(static::GZIP_LEVEL);
            $swooleResponse->end($response->getContent());
            unset($response, $swooleRequestServer);
        } catch (HttpException $e) {
            $swooleResponse->status($e->getStatusCode());
            $swooleResponse->end($this->isDebug() ? $e->getMessage() : static::SERVER_INTERVAL_ERROR);
        } catch (Exception $e) {
            $swooleResponse->status(500);
            $swooleResponse->end($this->isDebug() ? $e->getMessage() : static::SERVER_INTERVAL_ERROR);
        }
    }

    /**
     * @param SwooleServerRequest $request
     * @return Response
     */
    abstract public function doRequest(SwooleServerRequest $request);
}