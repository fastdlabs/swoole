<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/17
 * Time: 下午5:49
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

include __DIR__ . '/../../vendor/autoload.php';

use FastD\Swoole\Server\TcpServer;

$tcp = TcpServer::create('127.0.0.1', '9321');


 