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

use FastD\Swoole\Server\ServerInterface;

/**
 * Class HandlerAbstract
 *
 * @package FastD\Swoole\Handler
 */
abstract class HandlerAbstract implements HandlerInterface
{
    /**
     * @var ServerInterface
     */
    protected $server;

    /**
     * @param ServerInterface $serverInterface
     * @return $this
     */
    public function handle(ServerInterface $serverInterface)
    {
        $this->server = $serverInterface;

        $handles = get_class_methods($this);

        foreach ($handles as $value) {
            if ('on' == substr($value, 0, 2)) {
                $serverInterface->on(lcfirst(substr($value, 2)), [$this, $value]);
            }
        }

        return $this;
    }

    /**
     * @param $name
     * @return void
     */
    public function rename($name)
    {
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($name);
        } else if (function_exists('swoole_set_process_name')) {
            swoole_set_process_name($name);
        }
    }
}