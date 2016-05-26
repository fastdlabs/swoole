<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/1/29
 * Time: 下午10:43
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Monitor;

use FastD\Swoole\Server\Server;
use FastD\Packet\PacketException;
use FastD\Packet\Packet;
use FastD\Swoole\SwooleInterface;

abstract class Monitor implements MonitorInterface
{
    /**
     * @var string
     */
    protected $host = '127.0.0.1';

    /**
     * @var int|string
     */
    protected $port = '9599';

    /**
     * @var int
     */
    protected $mode = SwooleInterface::SWOOLE_SOCK_UDP;

    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var \swoole_server_port
     */
    protected $server_port;

    /**
     * Manager constructor.
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->setServer($server);
    }

    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param Server $server
     * @return $this
     */
    public function setServer(Server $server)
    {
        $this->server = $server;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int|string $port
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param int $mode
     * @return $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function bootstrap()
    {
        $this->server_port = $this->server->getServer()->listen($this->getHost(), $this->getPort(), $this->getMode());

        $this->server_port->on('receive', [$this, 'onReceive']);

        return $this;
    }

    /**
     * @return \swoole_server_port
     */
    public function getServerPort()
    {
        return $this->server_port;
    }

    /**
     * @param \swoole_server $server
     * @param int $fd
     * @param int $from_id
     * @param string $data
     * @return void
     */
    public function onReceive(\swoole_server $server, int $fd, int $from_id, string $data)
    {
        try {
            $action = $this->getAction($data);
        } catch (PacketException $e) {
            $action = 'status';
        }

        switch ($action) {
            case 'stop':
                $server->send($fd, Packet::encode([
                    'msg' => sprintf('Server[%s] is shutdown...', $this->server->getPid())
                ]), $from_id);
                $this->server->getServer()->shutdown();
                break;
            case 'reload':
                $server->send($fd, Packet::encode([
                    'msg' => sprintf('Server[%s] is reloading...', $this->server->getPid())
                ]), $from_id);
                $this->server->getServer()->reload();
                break;
            case 'status':
            default:
                $server->send($fd, Packet::encode([
                    'state' => $this->server->getServer()->stats(),
                    'connections' => $this->server->getServer()->connections,
                ]), $from_id);
        }
    }

    /**
     * @param $data
     * @return mixed
     * @throws \FastD\Packet\PacketException
     */
    public function getAction($data)
    {
        $data = Packet::decode($data);

        return $data;
    }
}