<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/12
 * Time: 下午5:44
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Rpc;

use FastD\Swoole\ProtocolInterface;
use FastD\Swoole\TcpServer\Tcp;

class RpcServer extends Tcp implements RpcInterface
{
    protected $functions;

    protected $data;

    protected $protocol;

    public function addCallback($name, $callback)
    {
        $this->functions[$name] = $callback;

        return $this;
    }

    public function getCallback($name)
    {
        return $this->hasCallback($name) ? $this->functions[$name] : null;
    }

    public function hasCallback($name)
    {
        return isset($this->functions[$name]);
    }

    public function allCallback()
    {
        return $this->functions;
    }

    public function setProtocol(ProtocolInterface $protocolInterface)
    {
        $this->protocol = $protocolInterface;

        return $this;
    }

    public function getProtocol()
    {
        return $this->protocol;
    }

    public function decode(ProtocolInterface $protocolInterface, $data)
    {
        return $this->data = $protocolInterface->decode($data);
    }

    public function encode(ProtocolInterface $protocolInterface, $data)
    {
        return $protocolInterface->encode($data);
    }
}