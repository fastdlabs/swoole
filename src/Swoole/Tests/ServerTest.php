<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/16
 * Time: 下午10:22
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Tests;

use FastD\Swoole\Server\TcpServer;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $server = TcpServer::create('0.0.0.0', '9321');

        print_r($server);
    }

    public function testTcpServer()
    {
        TcpServer::create('0.0.0.0', '9321')->start();
    }
}
