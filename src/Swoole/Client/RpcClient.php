<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/3/7
 * Time: 下午6:14
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Client;

use FastD\Swoole\Protocol\Packet;
use FastD\Swoole\SwooleInterface;

/**
 * Class Rpc
 *
 * @package FastD\Swoole\Client
 */
class RpcClient extends Client
{
    /**
     * @var array
     */
    protected $connects = [];

    /**
     * RpcClient constructor.
     * @param $host
     * @param $port
     */
    public function __construct($host, $port)
    {
        parent::__construct(SwooleInterface::SWOOLE_SOCK_TCP, SwooleInterface::SWOOLE_SYNC);

        $this->connect($host, $port);
    }

    /**
     * @param $name
     * @param array $args
     */
    public function call($name, array $args = [])
    {
        $this->send([
            'name' => $name,
            'args' => $args
        ]);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function send($data)
    {
        $packet = Packet::packet($data);

        return parent::send($packet->toJson());
    }

    /**
     * @return mixed
     */
    public function receive()
    {
        $data = parent::receive();

        return Packet::packet($data, Packet::PACKET_JSON, true)->toArray();
    }

    /**
     * @param      $host
     * @param      $port
     * @param null $flag
     * @return mixed
     */
    public function connect($host, $port, $flag = null)
    {
        $name = $host . ':' . $port;
        if (!isset($this->connects[$name])) {
            parent::connect($host, $port, $flag);
            $this->connects[$name] = true;
        }
    }
}