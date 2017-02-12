<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Async;

use FastD\Swoole\Client;
use swoole_http_client;

/**
 * Class AsyncHttp
 *
 * @package FastD\Swoole\Async
 */
class Http extends Client
{
    /**
     * AsyncHttp constructor.
     *
     * @param $address
     * @param $mode
     * @param bool $ssl
     */
    public function __construct($address, $mode, $ssl = false)
    {
        $info = $this->parse($address);

        $this->host = $info['host'];
        $this->port = $info['port'];

        $this->client = new swoole_http_client($this->host, $this->port, $ssl);
    }

    /**
     * @param $method
     * @return $this
     */
    public function withMethod($method)
    {
        $this->client->setMethod($method);

        return $this;
    }

    /**
     * @param $headers
     * @return $this
     */
    public function withHeaders($headers)
    {
        $this->client->setHeaders($headers);

        return $this;
    }

    /**
     * @param array $cookies
     * @return $this
     */
    public function withCookies(array $cookies)
    {
        $this->client->setCookies($cookies);

        return $this;
    }

    /**
     * @param $callback
     * @param int $timeout
     * @return $this
     */
    public function connect($callback, $timeout = 5)
    {
        // TODO: Implement connect() method.
    }

    /**
     * @param $callback
     * @return $this
     */
    public function receive($callback)
    {
        // TODO: Implement receive() method.
    }

    /**
     * @param $callback
     * @return $this
     */
    public function error($callback)
    {
        // TODO: Implement error() method.
    }

    /**
     * @param $callback
     * @return mixed
     */
    public function close($callback)
    {
        // TODO: Implement close() method.
    }

    /**
     * @return mixed
     */
    public function resolve()
    {
        // TODO: Implement resolve() method.
    }
}