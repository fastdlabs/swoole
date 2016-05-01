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

namespace FastD\Swoole\Client;

use FastD\Swoole\Handler\HandlerInterface;
use FastD\Swoole\Manager\Output;
use FastD\Swoole\SwooleInterface;

/**
 * Class Client
 *
 * @package FastD\Swoole\Client
 */
class Client implements ClientInterface
{
    use Output;

    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * @var \swoole_client
     */
    protected $client;

    /**
     * Client constructor.
     *
     * @param $mode
     * @param $async
     */
    public function __construct($mode = SwooleInterface::SWOOLE_SOCK_TCP, $async = SwooleInterface::SWOOLE_SYNC)
    {
        $this->client = new \swoole_client($mode, $async);
    }

    /**
     * @param      $host
     * @param      $port
     * @param null $flag
     * @return mixed
     */
    public function connect($host, $port, $flag = null)
    {
        return $this->client->connect($host, $port);
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
     * @param HandlerInterface $handlerInterface
     * @return $this
     */
    public function handle(HandlerInterface $handlerInterface)
    {
        $this->handler = $handlerInterface->handle($this);

        return $this;
    }

    /**
     * @param $name
     * @param $callback
     * @return mixed
     */
    public function on($name, $callback)
    {
        $this->client->on($name, $callback);
    }
}