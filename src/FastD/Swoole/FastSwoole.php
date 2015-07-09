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

class FastSwoole
{
    protected $protocol;

    public function __construct($protocol, array $config = ['worker_num' => 1])
    {
        $this->protocol = $protocol;
    }

    public static function create($protocol, array $config = ['worker_num' => 1])
    {
        return new static($protocol, $config);
    }

    public function run()
    {

    }

    public function daemonize()
    {

    }
}