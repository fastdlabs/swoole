<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */


use FastD\Swoole\Server;


class TcpServer extends Server
{
    /**
     * Please return swoole configuration array.
     *
     * @return array
     */
    public function configure()
    {

    }
}

class ServerTest extends PHPUnit_Framework_TestCase
{
    public function testNewServer()
    {
        $server = new TcpServer('foo');

        $this->assertEquals('127.0.0.1', $server->getHost());
        $this->assertEquals('9527', $server->getPort());
        $this->assertEquals('foo', $server->getName());
        $this->assertNull($server->getPid());
        $this->assertNull($server->getSwoole());
    }

    public function testServerBootstrap()
    {
        $server = new TcpServer('foo');
        $this->assertNull($server->getSwoole());
        $server->daemon();
        $server->bootstrap();
        $this->assertEquals('127.0.0.1', $server->getSwoole()->host);
        $this->assertEquals(9527, $server->getSwoole()->port);
        $this->assertEquals('/var/run/foo.pid', $server->getPid());
        $this->assertEquals([
            'daemonize' => true
        ], $server->getSwoole()->setting);
    }
}
