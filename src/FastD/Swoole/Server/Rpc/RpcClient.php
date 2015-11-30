<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/12
 * Time: 下午5:47
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Rpc;

use FastD\Swoole\Client\Client;
use FastD\Swoole\ProtocolInterface;

class RpcClient extends Client implements RpcInterface
{
    /**
     * @var ProtocolInterface
     */
    protected $protocol;

    /**
     * @return mixed
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @param mixed $protocol
     * @return $this
     */
    public function setProtocol(ProtocolInterface $protocol)
    {
        $this->protocol = $protocol;
        return $this;
    }

    public function send($action, array $arguments = [])
    {
        return $this->client->send($this->encode($this->protocol, [RpcInterface::RPC_ACTION => $action, RpcInterface::RPC_ARGS => $arguments]));
    }

    public function decode(ProtocolInterface $protocolInterface, $data)
    {
        return $protocolInterface->decode($data);
    }

    public function encode(ProtocolInterface $protocolInterface, $data)
    {
        return $protocolInterface->encode($data);
    }
}