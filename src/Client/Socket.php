<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Client;


class Socket
{
    public $socket;

    protected $host;

    protected $port;

    protected $callbacks = [];

    public function __construct($address)
    {
        $info = parse_url($address);

        $this->host = $info['host'];

        $this->port = $info['port'];

        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    }

    /**
     * @param $callback
     * @param int $timeout
     * @return $this
     */
    public function connect($callback, $timeout = 5)
    {
        $this->callbacks['connect'] = $callback;

        return $this;
    }

    /**
     * @param $callback
     * @return $this
     */
    public function receive($callback)
    {
        $this->callbacks['receive'] = $callback;

        return $this;
    }

    /**
     * @param $data
     * @return int
     */
    public function send($data)
    {
        return socket_write($this->socket, $data, strlen($data));
    }

    /**
     * @param $callback
     * @return $this
     */
    public function error($callback)
    {
        $this->callbacks['error'] = $callback;

        return $this;
    }

    /**
     * @param $callback
     * @return mixed
     */
    public function close($callback)
    {
        $this->callbacks['close'] = $callback;

        return $this;
    }

    /**
     * @return mixed
     */
    public function resolve()
    {
        if (false == socket_connect($this->socket, $this->host, $this->port)) {
            return call_user_func($this->callbacks['error'], socket_last_error($this->socket));
        }

        $result = call_user_func($this->callbacks['connect'], $this);

        if (false === $result) {
            return call_user_func($this->callbacks['error'], socket_last_error($this->socket));
        }

        call_user_func($this->callbacks['receive'], socket_read($this->socket, 2048));

        return $result;
    }
}