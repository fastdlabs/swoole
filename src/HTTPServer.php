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
use FastD\Http\SwooleServerRequest;
use FastD\Swoole\ServerAbstract;
use swoole_http_request;
use swoole_http_response;
use swoole_http_server;
use swoole_server;

/**
 * Class HTTPServer
 * @package FastD\Swoole\Server
 */
abstract class HTTPServer extends ServerAbstract
{
    const SERVER_INTERVAL_ERROR = 'Server Interval Error';

    /**
     * @var string
     */
    protected $protocol = 'http';

    /**
     * @return \swoole_http_server
     */
    public function initSwoole(): swoole_server
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
            $this->sendHeader($swooleResponse, $response);
            $swooleResponse->status($response->getStatusCode());
            $swooleResponse->end((string) $response->getBody());
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
     * @param swoole_server $server
     * @param $fd
     * @param $from_id
     */
    public function onConnect(swoole_server $server, int $fd, int $from_id): void
    {

    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param $fromId
     */
    public function onClose(swoole_server $server, int $fd, int $fromId): void
    {

    }

    /**
     * @param swoole_server $server
     * @param int $fd
     * @param int $reactor_id
     * @param string $data
     */
    public function onReceive(swoole_server $server, int $fd, int $reactor_id, string $data): void
    {

    }

    /**
     * @param swoole_server $server
     * @param string $data
     * @param array $client_info
     */
    public function onPacket(swoole_server $server, string $data, array $client_info): void
    {

    }

    /**
     * @param swoole_server $server
     * @param int $src_worker_id
     * @param string $message
     */
    public function onPipeMessage(swoole_server $server, int $src_worker_id, string $message): void
    {

    }

    /**
     * @param swoole_server $server
     * @param $taskId
     * @param $workerId
     * @param $data
     * @return mixed
     */
    public function onTask(swoole_server $server, int $taskId, int $workerId, string $data): void
    {

    }

    /**
     * @param swoole_server $server
     * @param $taskId
     * @param $data
     * @return mixed
     */
    public function onFinish(swoole_server $server, int $taskId, string $data): void
    {

    }
}