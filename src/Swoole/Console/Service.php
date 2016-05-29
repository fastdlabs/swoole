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

use FastD\Packet\Packet;
use FastD\Swoole\Client\Client;
use FastD\Swoole\Server\Server;
use FastD\Swoole\Watch\Watcher;

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
     * @var Client
     */
    protected $client;

    /**
     * @var \FastD\Swoole\Monitor\Manager
     */
    protected $monitor;

    /**
     * Service constructor.
     * @param Server $server
     */
    public function __construct(Server $server = null)
    {
        $this->server= $server;

        $this->monitor = $server->getMonitor();

        if (null !== $this->monitor) {
            $this->client = new Client($this->monitor->getSock());
        }
    }

    /**
     * @param $cmd
     * @return mixed
     * @throws \FastD\Packet\PacketException
     */
    protected function send($cmd)
    {
        $this->client->connect($this->monitor->getHost(), $this->monitor->getPort());

        $this->client->send(Packet::encode($cmd));

        $receive = Packet::decode($this->client->receive());

        $this->client->close();

        return $receive['ret'];
    }

    /**
     * @return void
     */
    public function start()
    {
        try {
            $this->server->start();
        } catch (\Exception $e) {
            Output::output($e->getMessage());
        }
    }

    /**
     * @return int
     */
    public function shutdown()
    {
        if (null !== $this->monitor) {
            $receive = $this->send([
                'cmd' => 'stop'
            ]);

            Output::output($receive['msg']);

            return 0;
        }

        $pid = $this->server->getPid();
        posix_kill($pid, SIGTERM);
        return 0;
    }

    /**
     * @return int
     */
    public function reload()
    {
        if (null !== $this->monitor) {
            $receive = $this->send([
                'cmd' => 'reload'
            ]);

            Output::output($receive['msg']);

            return 0;
        }

        $pid = $this->server->getPid();
        posix_kill($pid, SIGUSR1);
        return 0;
    }

    /**
     * @return int
     */
    public function status()
    {
        if (null !== $this->monitor) {
            $data = $this->send([
                'cmd' => 'status'
            ]);

            print_r($data);

            return 0;
        }

        return 0;
    }

    /**
     * @param array $directories
     * @return void
     */
    public function watch(array $directories = ['.'])
    {
        $watcher = new Watcher();

        $self = $this;

        $watcher->watch($directories, function () use ($self) {
            $self->reload();
        });

        unset($watcher);
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