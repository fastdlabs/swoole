<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Coroutine;


use Swoole\Coroutine\Client;

/**
 * Class TCP
 * @package FastD\Swoole\Coroutine
 */
class TCP
{
    /**
     * Client constructor.
     *
     * @param $address
     * @param $mode
     */
    public function __construct($address, $mode = SWOOLE_SOCK_TCP)
    {
        $info = parse_url($address);

        $this->host = $info['host'];
        $this->port = $info['port'];

        $this->client = new Client($mode);
    }

    /**
     * @param float $timeout
     * @return $this
     */
    public function connect($timeout = 0.5)
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
}