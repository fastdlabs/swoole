<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;

use FastD\Swoole\Tools\Scheme;
use swoole_client;

/**
 * Class Client
 *
 * @package FastD\Swoole
 */
abstract class Client
{
    use Scheme;

    /**
     * @var swoole_client
     */
    protected $client;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $port;

    /**
     * @var int
     */
    protected $timeout = 5;

    /**
     * Client constructor.
     *
     * @param $address
     * @param $mode
     */
    public function __construct($address, $mode = SWOOLE_SOCK_TCP)
    {
        $info = $this->parse($address);

        $this->host = $info['host'];
        $this->port = $info['port'];

        $this->client = new swoole_client($info['sock']);
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

    /**
     * @param $data
     * @return mixed
     */
    public function send($data)
    {
        return $this->client->send($data);
    }

    /**
     * @param $host
     * @param $port
     * @param $data
     * @return mixed
     */
    public function sendTo($host, $port, $data)
    {
        return $this->client->sendto($host, $port, $data);
    }

    /**
     * @param $callback
     * @param int $timeout
     * @return $this
     */
    abstract public function connect($callback, $timeout = 5);

    /**
     * @param $callback
     * @return $this
     */
    abstract public function receive($callback);

    /**
     * @param $callback
     * @return $this
     */
    abstract public function error($callback);

    /**
     * @param $callback
     * @return mixed
     */
    abstract public function close($callback);

    /**
     * @return mixed
     */
    abstract public function resolve();
}