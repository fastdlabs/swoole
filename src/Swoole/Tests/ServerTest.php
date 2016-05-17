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
    public function testConfig()
    {
        $server = TcpServer::create('0.0.0.0', '9321');

        $reflection = new \ReflectionClass($server);

        $property = $reflection->getProperty('config');

        $property->setAccessible(true);

        $server->configure([
            'log_file' => '/tmp/fd.log'
        ]);

        $config = $property->getValue($server);

        $this->assertEquals('/tmp/fd.log', $config['log_file']);

        $server->configure([
            'log_file' => 'tmp/fd.log'
        ]);

        $config = $property->getValue($server);

        $this->assertEquals($_SERVER['PWD'] . DIRECTORY_SEPARATOR . 'tmp/fd.log', $config['log_file']);

        $server->configure([
            'pid_file' => '/tmp/fd.pid'
        ]);

        $property = $reflection->getProperty('pid_file');

        $property->setAccessible(true);

        $pid = $property->getValue($server);

        $this->assertEquals('/tmp/fd.pid', $pid);
    }

    public function testHandles()
    {
        $server = TcpServer::create('0.0.0.0', '9322');

        $server->on('receive', function () {});

        $reflection = new \ReflectionClass($server);

        $property = $reflection->getProperty('handles');

        $property->setAccessible(true);

        $handles = $property->getValue($server);

        $this->assertEquals($handles, ['receive' => function () {}]);

        $server->handle(new TestHandler());

        $handles = $property->getValue($server);

        $this->assertEquals(['receive', 'start', 'shutdown'], array_keys($handles));
    }
}
