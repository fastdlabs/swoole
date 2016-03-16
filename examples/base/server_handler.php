<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/11
 * Time: 上午10:12
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

use FastD\Swoole\Protocol\Packet;

/**
 * Class SwooleHandler
 *
 * @package FastD\Swoole\Handler
 */
class ServerHandler extends \FastD\Swoole\Handler\ServerHandlerAbstract
{
    /**
     * @param \swoole_server $server
     * @return mixed
     */
    public function onManagerStart(\swoole_server $server)
    {
        $this->rename($this->server->getName() . ' manager');
    }

    /**
     * @param \swoole_server $server
     * @param                $worker_id
     * @return mixed
     */
    public function onWorkerStart(\swoole_server $server, $worker_id)
    {
        $this->rename($this->server->getName() . ' worker');
    }

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @return mixed
     */
    public function onConnect(\swoole_server $server, $fd, $from_id)
    {
        echo 'connection' . PHP_EOL;
    }

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @param                $data
     * @return mixed
     * fd = fopen Resources
     * socket
     */
    public function onReceive(\swoole_server $server, $fd, $from_id, $data)
    {
        $packet = Packet::packet($data, Packet::PACKET_JSON, true);
        print_r($packet->toArray());
        $server->send($fd, $packet->toRaw());
        $server->close($fd);
    }

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @return mixed
     */
    public function onClose(\swoole_server $server, $fd, $from_id)
    {
        echo 'close' . PHP_EOL;
    }

    /**
     * @param \swoole_server $server
     * @param $data
     * @param array $client_info
     * @return mixed
     */
    public function onPacket(\swoole_server $server, $data, array $client_info)
    {

    }
}