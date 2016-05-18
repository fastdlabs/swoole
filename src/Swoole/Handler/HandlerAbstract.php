<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/1/29
 * Time: 下午11:30
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Handler;

use FastD\Swoole\Server\Server;

/**
 * Class HandlerAbstract
 *
 * @package FastD\Swoole\Handler
 */
abstract class HandlerAbstract implements HandlerInterface
{
    /**
     * @var Server
     */
    protected $server;

    public function handle(Server $server)
    {
        $this->server = $server;

        $handles = get_class_methods($this);

        foreach ($handles as $value) {
            if ('on' == substr($value, 0, 2)) {
                $server->on(lcfirst(substr($value, 2)), [$this, $value]);
            }
        }

        return $this;
    }
}