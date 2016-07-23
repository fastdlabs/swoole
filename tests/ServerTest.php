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
    protected $workspace_dir;

    public function setUp()
    {
        $this->workspace_dir = isset($_SERVER['PWD']) ? $_SERVER['PWD'] : realpath('.');
    }

    public function testDefaultParseServerIniFileConfig()
    {
        $server = TcpServer::create();

        $this->assertEquals($server->getWorkSpace(), $this->workspace_dir);

        $this->assertEquals($server->getPidFile(), $this->workspace_dir . '/run/fds.pid');

        $this->assertEquals($server->getLogFile(), $this->workspace_dir . '/var/fds.log');

        $this->assertEquals('127.0.0.1', $server->getHost());

        $this->assertEquals('9527', $server->getPort());

        $this->assertEquals([
            'start','shutdown', 'managerStart', 'managerStop', 'workerStart', 'workerStop', 'workerError'
        ], array_keys($server->getHandles()));

        unset($server);
    }

    public function testConstructionArguments()
    {
        $server = TcpServer::create('0.0.0.0', '1234');

        $this->assertEquals('0.0.0.0', $server->getHost());

        $this->assertEquals('1234', $server->getPort());

        unset($server);
    }

    public function testConfiguration()
    {
        $server = TcpServer::create();

        $server->configure([
            'host' => '11.11.11.22',
            'port' => '9999',
            'pid' => '/tmp/server.pid'
        ]);

        $this->assertEquals('11.11.11.22', $server->getHost());

        $this->assertEquals('9999', $server->getPort());

        $this->assertEquals('/tmp/server.pid', $server->getPidFile());

        unset($server);
    }
}
