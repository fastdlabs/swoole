<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Monitor;

use FastD\Packet\Binary;
use FastD\Swoole\Server\HttpServer;

/**
 * Class Monitor
 *
 * @package FastD\Swoole\Monitor
 */
class Monitor extends HttpServer
{
    /**
     * @param \swoole_server $server
     * @param int $fd
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    public function doWork(\swoole_server $server, int $fd, int $from_id, string $data)
    {
        $data = Binary::decode($data);
        print_r($data);
        $server->send($fd, 'hello world');
        $server->close($fd);
    }

    /**
     * @param \swoole_http_request $request
     * @param \swoole_http_response $response
     * @return mixed
     */
    public function doRequest(\swoole_http_request $request, \swoole_http_response $response)
    {
        $response->end('hello world');
    }

    /**
     * @param \swoole_server $server
     * @param string $data
     * @param array $client_info
     */
    public function doPacket(\swoole_server $server, string $data, array $client_info)
    {
        return;
    }
}