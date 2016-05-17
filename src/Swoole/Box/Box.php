<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/17
 * Time: 下午4:04
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Invoker;

use FastD\Swoole\Server\Server;

class Box
{
    protected $manager;

    protected $server;

    protected static $box;

    final protected function __construct(Server $server)
    {
        $this->server = $server;
    }

    public static function create(Server $server)
    {
        if (null === static::$box) {
            static::$box = new static($server);
        }

        return static::$box;
    }

    public function start()
    {

    }

    public function stop()
    {

    }
}