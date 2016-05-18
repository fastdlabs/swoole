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

namespace FastD\Swoole\Manager;

use FastD\Swoole\Server\Server;

/**
 * Server Manager.
 *
 * Class ServerManager
 *
 * @package FastD\Swoole\Manager
 */
class ServerManager implements MonitorInterface
{
    protected $servers = [];

    public function __construct()
    {

    }

    public function addServer($name, Server $server)
    {
        $this->servers[$name] = $server;
    }

    public function listen($host, $port, $mode, $type = null)
    {
        // TODO: Implement listen() method.
    }

    public function start($name = null)
    {
        // TODO: Implement start() method.
    }

    public function reload($name = null)
    {
        // TODO: Implement reload() method.
    }

    public function shutdown($name = null)
    {
        // TODO: Implement shutdown() method.
    }

    /**
     * @param $name
     * @param $callback
     * @return $this
     */
    public function on($name, $callback)
    {
        // TODO: Implement on() method.
    }

    /**
     * @param array $configure
     * @return $this
     */
    public function configure(array $configure)
    {
        // TODO: Implement configure() method.
    }
}