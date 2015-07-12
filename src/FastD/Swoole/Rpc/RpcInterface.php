<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/12
 * Time: 下午5:45
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Rpc;

use FastD\Swoole\ProtocolInterface;
use FastD\Swoole\SwooleInterface;

interface RpcInterface extends SwooleInterface
{
    const RPC_ACTION = 'action';
    const RPC_ARGS = 'args';

    public function decode(ProtocolInterface $protocolInterface, $data);

    public function encode(ProtocolInterface $protocolInterface, $data);
}