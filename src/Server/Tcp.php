<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;

use FastD\Swoole\Server;
use swoole_server;

/**
 * Class Tcp
 *
 * @package FastD\Swoole\Server
 */
abstract class Tcp extends Server
{
    /**
     * 服务器同时监听TCP/UDP端口时，收到TCP协议的数据会回调onReceive，收到UDP数据包回调onPacket
     *
     * @param swoole_server $server
     * @param $fd
     * @param $from_id
     * @param $data
     * @return mixed
     */
    public function onReceive(swoole_server $server, $fd, $from_id, $data)
    {
        try {
            $content = $this->doWork($server, $fd, $data, $from_id);
            $server->send($fd, $content);
            $server->close($fd);
        } catch (\Exception $e) {
            $server->send($fd, sprintf("Error: %s\nFile: %s \nCode: %s\nLine: %s\r\n\r\n",
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getCode(),
                    $e->getLine()
                )
            );
            $server->close($fd);
        }
    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param $data
     * @param $from_id
     * @return mixed
     */
    abstract public function doWork(swoole_server $server, $fd, $data, $from_id);
}