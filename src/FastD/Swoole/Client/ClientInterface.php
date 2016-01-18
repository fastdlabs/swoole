<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/12
 * Time: 下午4:38
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Client;

use FastD\Swoole\Handler\ClientHandlerInterface;

interface ClientInterface
{
    public function handle(ClientHandlerInterface $clientHandlerInterface);

    public function send($data);

    public function connect($host, $port, $flag = null);

    public function receive();

    public function close();
}