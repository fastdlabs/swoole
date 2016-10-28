<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;

use FastD\Swoole\Exceptions\AddressIllegalException;
use swoole_client;

/**
 * Class Client
 *
 * @package FastD\Swoole\Client
 */
abstract class Client
{
    /**
     * @var swoole_client
     */
    protected $client;

    /**
     * @var string
     */
    protected $sockType;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $port;

    /**
     * Client constructor.
     *
     * @param $address
     * @param $mode
     */
    public function __construct($address, $mode = SWOOLE_SOCK_TCP)
    {
        $this->parseProtocol($address);

        $this->client = new swoole_client($mode);
    }

    /**
     * @param $address
     * @return $this
     */
    protected function parseProtocol($address)
    {
        if (false === ($info = parse_url($address))) {
            throw new AddressIllegalException($address);
        }

        $this->sockType = $info['scheme'];
        $this->host = $info['host'];
        $this->port = isset($info['port']) ? $info['port'] : 80;

        return $this;
    }

    /**
     * @return string
     */
    public function getSockType()
    {
        return $this->sockType;
    }

    /**
     * @param int $timeout
     * @return mixed
     */
    public function connect($timeout = 5)
    {
        return $this->client->connect($this->host, $this->port, $timeout);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function send($data)
    {
        return $this->client->send($data);
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
     * @param $handler
     * @return mixed
     */
    public function on($name, $handler)
    {
        $this->client->on($name, $handler);

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