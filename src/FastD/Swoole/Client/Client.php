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

use FastD\Swoole\Context;
use FastD\Swoole\Handler\ClientHandlerInterface;
use FastD\Swoole\SwooleHandlerInterface;

class Client implements ClientInterface
{
    const ASYNC = SWOOLE_SOCK_ASYNC;

    const SYNC = SWOOLE_SOCK_SYNC;

    protected $handler;

    protected $client;

    public function __construct(ClientHandlerInterface $clientHandlerInterface = null, $mode = SWOOLE_SOCK_TCP, $async = Client::SYNC)
    {
        $this->client = new \swoole_client($mode, $async);

        $this->handle($clientHandlerInterface);
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
     * @param ClientHandlerInterface $clientHandlerInterface
     */
    public function handle(ClientHandlerInterface $clientHandlerInterface = null)
    {
        if (null !== $clientHandlerInterface) {
            $this->client->on('connect', [$clientHandlerInterface, 'onConnect']);
            $this->client->on('receive', [$clientHandlerInterface, 'onReceive']);
            $this->client->on('error', [$clientHandlerInterface, 'onError']);
            $this->client->on('close', [$clientHandlerInterface, 'onClose']);
        }
    }
}