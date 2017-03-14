<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;


use swoole_client;

/**
 * Class Client
 *
 * @package FastD\Swoole
 */
class Client
{
    protected $swoole;

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
    protected $socketType = SWOOLE_SOCK_TCP;

    /**
     * @var int
     */
    protected $timeout = 1;

    /**
     * Client constructor.
     *
     * @param $address
     * @param $socketType
     */
    public function __construct($address, $socketType = SWOOLE_SOCK_TCP)
    {
        $info = parse_url($address);

        $this->host = $info['host'];
        $this->port = $info['port'];
        $this->socketType = $socketType;

        $this->swoole = new swoole_client($this->socketType, SWOOLE_SOCK_SYNC);
    }

    /**
     * @param $address
     * @param $socketType
     * @return static
     */
    public static function create($address, $socketType = SWOOLE_SOCK_TCP)
    {
        return new static($address, $socketType);
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
     * @param swoole_client $client
     * @return mixed
     */
    public function connect(swoole_client $client){}

    /**
     * @param swoole_client $client
     * @param string $data
     * @return mixed
     */
    public function receive(swoole_client $client, $data){}

    /**
     * @param swoole_client $client
     * @return mixed
     */
    public function error(swoole_client $client){}

    /**
     * @param swoole_client $client
     * @return mixed
     */
    public function close(swoole_client $client){}

    /**
     * @param $data
     * @param bool $async
     * @param bool $keep
     * @return mixed
     */
    public function send($data = null, $async = false, $keep = false)
    {
        if (true === $async || true === $keep) {
            $sync = true === $async ? SWOOLE_SOCK_ASYNC : SWOOLE_SOCK_ASYNC;
            $this->socketType = true === $keep ? ($this->socketType | SWOOLE_KEEP) : $this->socketType;
            $this->swoole = new swoole_client($this->socketType, $sync);
        }

        $client = $this->swoole;

        if (!$async) {
            if (!$client->connect($this->host, $this->port, $this->timeout)) {
                throw new \RuntimeException(socket_strerror($client->errCode));
            } else {
                $this->connect($client);
            }
            $client->send($data);
            $receive = $client->recv();
            call_user_func_array([$this, 'receive'], [$client, $receive]);
            return $receive;
        } else {
            $client->on("connect", function ($client) { call_user_func_array([$this, 'connect'], [$client]); });
            $client->on("receive", function ($client, $data) { call_user_func_array([$this, 'receive'], [$client, $data]); });
            $client->on("error", function ($client) { call_user_func_array([$this, 'error'], [$client]); });
            $client->on("close", function ($client) { call_user_func_array([$this, 'close'], [$client]); });
            $client->connect($this->host, $this->port, $this->timeout);
        }
        unset($client);
        return true;
    }
}