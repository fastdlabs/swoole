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
        $this->parseProtocol($address);

        $this->client = new swoole_client($mode, SWOOLE_SOCK_ASYNC);
    }

    /**
     * @param $callback
     * @return $this
     */
    public function onError($callback)
    {
        $this->on('error', $callback);

        return $this;
    }

    /**
     * @param $callback
     * @return $this
     */
    public function onConnect($callback)
    {
        $this->on('connect', $callback);

        return $this;
    }

    /**
     * @param $callback
     * @return $this
     */
    public function onReceive($callback)
    {
        $this->on('receive', $callback);

        return $this;
    }

    /**
     * @param $callback
     * @return $this
     */
    public function onClose($callback)
    {
        $this->on('close', $callback);

        return $this;
    }

    /**
     * @param null $callback
     * @param int $timeout
     * @return mixed
     */
    public function connect($callback = null, $timeout = 5)
    {
        if (!(null === $callback)) {
            $this->onConnect($callback);
        }

        return parent::connect($timeout);
    }
}