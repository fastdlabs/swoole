<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/21
 * Time: 下午8:29
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Console;

use FastD\Swoole\Server\Server;

/**
 * Service 管理脚本
 *
 * Class Service
 *
 * @package FastD\Swoole\Console
 */
class Service
{
    /**
     * @var static
     */
    protected static $service;

    /**
     * @var Server
     */
    protected $server;

    /**
     * Service constructor.
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server= $server;
    }

    /**
     * @return void
     */
    public function start()
    {
        $this->server->start();
    }

    /**
     * @return int
     */
    protected function getPid()
    {
        $pid = $this->server->getPidFile();
        if (!file_exists($pid)) {
            throw new \RuntimeException(sprintf('Pid file ["%s"] is not exists', $pid));
        }

        return (int) trim(file_get_contents($pid));
    }

    /**
     * @return void
     */
    public function stop()
    {
        $pid = $this->getPid();

        posix_kill($pid, SIGTERM);
    }

    /**
     * @return void
     */
    public function reload()
    {
        $pid = $this->getPid();

        posix_kill($pid, SIGUSR1);
    }

    /**
     * @return void
     */
    public function restart()
    {
        $this->stop();
        $this->start();
    }

    /**
     * @return void
     */
    public function status()
    {

    }

    /**
     * @param Server $server
     * @return static
     */
    public static function server(Server $server)
    {
        if (null === static::$service) {
            static::$service = new static($server);
        }

        return static::$service;
    }
}