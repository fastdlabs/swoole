<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Client\Async;

use FastD\Swoole\Client;
use swoole_client;

/**
 * Class AsyncClient
 *
 * @package FastD\Swoole\Async
 */
class AsyncClient extends Client
{
    /**
     * AsyncClient constructor.
     *
     * @param $address
     * @param $mode
     */
    public function __construct($address, $mode = SWOOLE_SOCK_TCP)
    {
        $info = $this->parse($address);

        $this->host = $info['host'];
        $this->port = $info['port'];

        $this->client = new swoole_client($mode, SWOOLE_SOCK_ASYNC);
    }

    /**
     * @param $callback
     * @param int $timeout
     * @return $this
     */
    public function connect($callback, $timeout = 5)
    {
        $this->client->on('connect', $callback);

        return $this;
    }

    /**
     * @param $callback
     * @return $this
     */
    public function receive($callback)
    {
        $this->client->on('receive', $callback);

        return $this;
    }

    /**
     * @param $callback
     * @return $this
     */
    public function error($callback)
    {
        $this->client->on('error', $callback);

        return $this;
    }

    public function close()
    {
        $this->client->on('close', function (swoole_client $client) {
            $client->close();
        });
    }

    /**
     * @return mixed
     */
    public function resolve()
    {
        $this->client->connect($this->host, $this->port, $this->timeout);
    }
}