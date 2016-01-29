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
use FastD\Swoole\SwooleInterface;

class Client implements ClientInterface, SwooleInterface
{
    protected $handler;

    protected $client;

    public function __construct($mode = SwooleInterface::SWOOLE_SOCK_TCP, $async = SwooleInterface::SWOOLE_SYNC)
    {
        $this->client = new \swoole_client($mode, $async);
    }

    public function connect($host, $port, $flag = null)
    {
        return $this->client->connect($host, $port);
    }

    public function send($data)
    {
        return $this->client->send($data);
    }

    public function receive()
    {
        return $this->client->recv();
    }

    public function close()
    {
        return $this->client->close();
    }

    /**
     * @param HandlerInterface $handlerInterface
     */
    public function handle(HandlerInterface $handlerInterface)
    {
        $handlerInterface->handle($this);
    }
}