<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/9
 * Time: 下午6:23
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole;

class Swoole
{
    /**
     * @var swoole_server
     */
    protected $server;

    protected $context;

    public function __construct(Context $context, $mode = SWOOLE_PROCESS, $sockType = SWOOLE_SOCK_TCP)
    {
        $this->server = new swoole_server($context->getSchema(), $context->getPort(), $mode, $sockType);

        $this->context = $context;
    }

    public static function create($protocol, array $config = ['worker_num' => 1])
    {
        return new static(new Context($protocol, $config));
    }

    public function run()
    {
        $this->server->set($this->context->all());

        $this->server->start();
    }

    public function daemonize()
    {
        $this->context->set('daemonize', true);

        return $this;
    }
}