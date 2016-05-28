<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/16
 * Time: 下午10:25
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Server;

/**
 * Class TcpServer
 *
 * @package FastD\Swoole\Server
 */
class TcpServer extends Server
{
    /**
     * @return \swoole_server
     */
    public function initSwooleServer()
    {
        return new \swoole_server($this->getHost(), $this->getPort(), $this->getMode(), $this->getSock());
    }
}