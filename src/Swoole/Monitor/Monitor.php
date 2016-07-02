<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/1/29
 * Time: 下午10:43
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Monitor;

use FastD\Packet\Binary;
use FastD\Swoole\Server\Server;

/**
 * Class Monitor
 *
 * @package FastD\Swoole\Monitor
 */
class Monitor extends Server
{
    const SERVER_NAME = 'fds monitor';

    protected $sockType = SWOOLE_SOCK_UDP;

    /**
     * @param \swoole_server $server
     * @param int $fd
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    public function doWork(\swoole_server $server, int $fd, int $from_id, string $data)
    {
        $data = Binary::decode($data);

        print_r($data);

        $server->send($fd, 'hello world');
        $server->close($fd);
    }
}