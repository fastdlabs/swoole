<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */


use FastD\Swoole\Server;
use FastD\Swoole\Server\TCPServer;


class TcpServerServer extends TCPServer
{
    /**
     * @param swoole_server $server
     * @param $fd
     * @param $data
     * @param $from_id
     * @return mixed
     */
    public function doWork(swoole_server $server, $fd, $data, $from_id)
    {
        // TODO: Implement doWork() method.
    }
}

class ServerTest extends PHPUnit_Framework_TestCase
{
    public function testNewServer()
    {
        $server = new TcpServerServer('foo', '127.0.0.1:9528');

        $this->assertEquals('127.0.0.1', $server->getHost());
        $this->assertEquals('9528', $server->getPort());
        $this->assertEquals('foo', $server->getName());
        $this->assertEquals('/tmp/foo.pid', $server->getPidFile());
        $this->assertNull($server->getSwoole());
    }

    public function testServerBootstrap()
    {
        $server = new TcpServerServer('foo', '127.0.0.1:9529');
        $this->assertNull($server->getSwoole());
        $server->daemon();
        $server->bootstrap();
        $this->assertEquals('127.0.0.1', $server->getSwoole()->host);
        $this->assertEquals(9529, $server->getSwoole()->port);
        $this->assertEquals('/tmp/foo.pid', $server->getPidFile());
        $this->assertEquals([
            'daemonize' => true,
            'task_worker_num' => 8,
            'task_tmpdir' => '/tmp',
            'pid_file' => '/tmp/foo.pid',
            'worker_num' => 8,
            'open_cpu_affinity' => true,
        ], $server->getSwoole()->setting);
    }

    public function testServerBootstrapConfig()
    {
        $server = new TcpServerServer('foo', 'tcp://127.0.0.1:9530', [
            'pid_file' => '/tmp/foo.pid',
        ]);
        $server->daemon();
        $server->bootstrap();
        $this->assertEquals([
            'daemonize' => true,
            'pid_file' => '/tmp/foo.pid',
            'task_worker_num' => 8,
            'task_tmpdir' => '/tmp',
            'worker_num' => 8,
            'open_cpu_affinity' => true,
        ], $server->getSwoole()->setting);
        $this->assertEquals('/tmp/foo.pid', $server->getPidFile());
    }
}
