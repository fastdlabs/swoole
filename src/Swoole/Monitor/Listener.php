<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/18
 * Time: 上午11:26
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Monitor;

use FastD\Swoole\Server\Server;
use FastD\Swoole\SwooleInterface;

class Listener implements SwooleInterface
{
    protected $host;

    protected $port;

    protected $mode;

    /**
     * @var \swoole_server
     */
    protected $server;

    public function __construct($host, $port, $mode)
    {
        $this->host = $host;

        $this->port = $port;

        $this->mode = $mode;
    }

    public function setServer(Server $server)
    {
        $this->server = $server->getServer()->listen($this->host, $this->port, $this->mode);
    }

    /**
     * @param $name
     * @param $callback
     * @return $this
     */
    public function on($name, $callback)
    {
        $this->server->on($name, $callback);
    }

    /**
     * @param array $configure
     * @return $this
     */
    public function configure(array $configure)
    {
        $this->server->set($configure);
    }
}