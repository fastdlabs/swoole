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

}

class ServerTest extends PHPUnit_Framework_TestCase
{
    public function testNewServer()
    {
        $server = new TcpServer('foo');

        $this->assertEquals('127.0.0.1', $server->getHost());
        $this->assertEquals('9527', $server->getPort());
        $this->assertEquals('foo', $server->getName());
        $this->assertEmpty($server->getPid());
        $this->assertNull($server->getSwoole());
    }

    public function testServerBootstrap()
    {
        $server = new TcpServer('foo', 'tcp://127.0.0.1:9529');
        $this->assertNull($server->getSwoole());
        $server->daemon();
        $server->bootstrap();
        $this->assertEquals('127.0.0.1', $server->getSwoole()->host);
        $this->assertEquals(9529, $server->getSwoole()->port);
        $this->assertEquals($server->getPort(), $server->getSwoole()->port);
        $this->assertEquals('/tmp/foo.pid', $server->getPid());
        $this->assertEquals([
            'pid_file' => '/tmp/foo.pid',
            'daemonize' => true,
            'worker_num' => 10,
            'task_worker_num' => 10,
        ], $server->getSwoole()->setting);
    }

    public function testServerBootstrapConfig()
    {
        $server = new TcpServer('foo', 'tcp://127.0.0.1:9528', [
            'pid_file' => '/tmp/foo.pid'
        ]);
        $server->daemon();
        $server->bootstrap();
        $this->assertEquals([
            'daemonize' => true,
            'pid_file' => '/tmp/foo.pid',
            'worker_num' => 10,
            'task_worker_num' => 10,
        ], $server->getSwoole()->setting);
        $this->assertEquals('/tmp/foo.pid', $server->getPid());
    }
}
