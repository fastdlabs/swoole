<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/17
 * Time: 下午7:01
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

$server = new swoole_server('127.0.0.1', 9501);

$port = $server->listen('127.0.0.1', 9502, SWOOLE_SOCK_TCP);
$port->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, 'listen');
    $serv->close($fd);
});

$server->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, 'server');
    $serv->close($fd);
});

$server->start();