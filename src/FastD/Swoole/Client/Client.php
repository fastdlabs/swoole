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
use FastD\Swoole\SwooleHandlerInterface;

class Client implements ClientInterface
{
    protected $client;

    /**
     * @var Context
     */
    protected $context;

    protected $on;

    protected $isHandle = false;

    public function __construct($mode = SWOOLE_SOCK_TCP, $async = SWOOLE_SOCK_ASYNC)
    {
        $this->client = new \swoole_client($mode, $async);
    }

    public function connect($protocol, $flag = null)
    {
        if (false === $this->isHandle) {
            $this->handle(new ClientHandler());
        }

        $this->context = new Context($protocol, ['flog' => $flag]);

        return $this->client->connect($this->context->getHost(), $this->context->getPort(), $flag);
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
     * @param      $name
     * @param null $callback
     * @return $this
     */
    public function on($name, $callback = null)
    {
        $this->client->on($name, $callback);

        return $this;
    }

    /**
     * @param Context $context
     * @return $this
     */
    public function setContext(Context $context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param SwooleHandlerInterface $swooleHandlerInterface
     * @return $this
     */
    public function handle(SwooleHandlerInterface $swooleHandlerInterface)
    {
        $swooleHandlerInterface->handle($this);

        $this->isHandle = true;
    }
}