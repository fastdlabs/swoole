<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/1/18
 * Time: 下午9:47
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

include __DIR__ . '/../../vendor/autoload.php';

use FastD\Swoole\Server\Server;
use FastD\Swoole\Handler\TcpHandlerAbstract;

class DemoServer extends Server
{
    /**
     * @return array
     */
    public function configure()
    {
        return [
            //to improve the accept performance ,suggest the number of cpu X 2
            //如果想提高请求接收能力，更改这个，推荐cpu个数x2
            'reactor_num' => 32,

            //packet decode process,change by condition
            //包处理进程，根据情况调整数量
            'worker_num' => 40,

            //the number of task logical process progcessor run you business code
            //实际业务处理进程，根据需要进行调整

            'log_file' =>'/tmp/sw_server.log',
        ];
    }
}

class Handler extends TcpHandlerAbstract
{
    /**
     * @param \swoole_server $server
     * @param $fd
     * @param $from_id
     * @param $data
     * @return mixed
     */
    public function onReceive(\swoole_server $server, $fd, $from_id, $data)
    {
        $server->send($fd, $data, $from_id);
        $server->close($fd);
    }

    /**
     * @param \swoole_server $server
     * @param $data
     * @param array $client_info
     * @return mixed
     */
    public function onPacket(\swoole_server $server, $data, array $client_info)
    {
        // TODO: Implement onPacket() method.
    }
}

$server = DemoServer::create();

$server->handle(new Handler());

$server->start();
