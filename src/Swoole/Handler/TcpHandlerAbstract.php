<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/3/7
 * Time: 下午5:18
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Handler;

/**
 * Class ServerHandlerAbstract
 *
 * @package FastD\Swoole\Handler
 */
abstract class TcpHandlerAbstract extends Handle
{
    /**
     * @param \swoole_server $server
     * @param $fd
     * @param $from_id
     * @param $data
     * @return mixed
     */
    abstract public function onReceive(\swoole_server $server, $fd, $from_id, $data);

    /**
     * @param \swoole_server $server
     * @param $data
     * @param array $client_info
     * @return mixed
     */
    abstract public function onPacket(\swoole_server $server, $data, array $client_info);
}