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
use FastD\Swoole\Tests\Handle\TestHandler;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function testIniConfig()
    {
        $server = TcpServer::create();

        print_r($server);
    }

    public function testInit()
    {
        $server = TcpServer::create('0.0.0.0', '1111');

        print_r($server);
    }

    public function testConfigure()
    {
        $server = TcpServer::create('0.0.0.0', '1112');

        $server->configure([
            'log_level' => 3,
            'log_file' => '/tmp/error.log'
        ]);

        print_r($server);
    }
}
