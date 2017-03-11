<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */


use FastD\Swoole\Server;
use FastD\Swoole\Server\TCP;


class TcpServer extends TCP
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
        $server = new TcpServer('foo');

        $this->assertEquals(get_local_ip(), $server->getHost());
        $this->assertEquals('9527', $server->getPort());
        $this->assertEquals('foo', $server->getName());
        $this->assertEquals('/tmp/foo.pid', $server->getPid());
        $this->assertNull($server->getSwoole());
    }

    public function testServerBootstrap()
    {
        $server = new TcpServer('foo');
        $this->assertNull($server->getSwoole());
        $server->daemon();
        $server->bootstrap();
        $this->assertEquals(get_local_ip(), $server->getSwoole()->host);
        $this->assertEquals(9527, $server->getSwoole()->port);
        $this->assertEquals('/tmp/foo.pid', $server->getPid());
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
        $server = new TcpServer('foo', 'tcp://127.0.0.1:9527', [
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
        $this->assertEquals('/tmp/foo.pid', $server->getPid());
    }
}
