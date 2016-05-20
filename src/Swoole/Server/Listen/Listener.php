<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/19
 * Time: 上午1:22
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Server\Listen;

use FastD\Swoole\Server\Server;
use FastD\Swoole\SwooleInterface;

/**
 * Class Listener
 *
 * @package FastD\Swoole\Server\Listen
 */
class Listener
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var int|string
     */
    protected $port;

    /**
     * @var int
     */
    protected $mode;

    /**
     * @var \swoole_server
     */
    protected $server;

    /**
     * Listener constructor.
     * @param $host
     * @param $port
     * @param int $mode
     */
    public function __construct($host, $port, $mode = SwooleInterface::SWOOLE_SOCK_UDP)
    {
        $this->host = $host;

        $this->port = $port;

        $this->mode = $mode;
    }

    /**
     * @param Server $server
     */
    public function setServer(Server $server)
    {
        $this->server = $server;

        $listen = $this->server->getServer()->listen($this->host, $this->port, $this->mode);

        $listen->on('receive', [$this, 'onReceive']);
        $listen->on('connect', [$this, 'onConnect']);
    }

    public function onReceive(\swoole_server $server, int $fd, int $from_id, string $data)
    {
        print_r($this->server->getServer()->connections);
    }

    public function onConnect(\swoole_server $server, $fd, $reactorId)
    {

    }
}