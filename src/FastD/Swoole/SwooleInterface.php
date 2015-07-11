<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/10
 * Time: 上午11:55
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */
namespace FastD\Swoole;

interface SwooleInterface
{
    public function status();

    public function start();

    public function stop();

    public function reload();

    public function handle(SwooleHandlerInterface $swooleHandlerInterface = null, array $on = ['onStart', 'onStop']);
}