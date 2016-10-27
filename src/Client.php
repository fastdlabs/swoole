<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/12
 * Time: 下午4:06
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole;

use swoole_client;

/**
 * Class Client
 *
 * @package FastD\Swoole\Client
 */
class Client
{
    /**
     * @var swoole_client
     */
    protected $client;

    /**
     * @var bool
     */
    protected $async = false;

    /**
     * Client constructor.
     *
     * @param $mode
     * @param $async
     */
    public function __construct($mode = SWOOLE_SOCK_TCP, $async = null)
    {
        $this->client = new swoole_client($mode, $async);
    }

    /**
     * @return bool
     */
    public function isAsync()
    {
        return $this->async;
    }

    /**
     * @param $host
     * @param $port
     * @param int $timeout
     * @return $this
     */
    public function connect($host, $port, $timeout = 5)
    {
        $this->client->connect($host, $port, $timeout);

        return $this;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function send($data)
    {
        $this->client->send($data);

        return $this->receive();
    }

    /**
     * @return mixed
     */
    public function receive()
    {
        return $this->client->recv();
    }

    /**
     * @return mixed
     */
    public function close()
    {
        return $this->client->close();
    }

    /**
     * @param $name
     * @param $callback
     * @return mixed
     */
    public function on($name, $callback)
    {
        $this->client->on($name, $callback);

        return $this;
    }

    /**
     * @param $configure
     * @return $this
     */
    public function configure($configure)
    {
        $this->client->set($configure);

        return $this;
    }
}