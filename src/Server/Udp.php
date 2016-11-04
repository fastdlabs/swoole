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
 * Class Udp
 *
 * @package FastD\Swoole\Server
 */
abstract class Udp extends Server
{
    /**
     * 服务器同时监听TCP/UDP端口时，收到TCP协议的数据会回调onReceive，收到UDP数据包回调onPacket
     *
     * @param swoole_server $server
     * @param string $data
     * @param array $client_info
     */
    public function onPacket(swoole_server $server, $data, array $client_info)
    {
        try {
            $content = $this->doPacket($server, $data, $client_info);
        } catch (\Exception $e) {
            $content = sprintf("Error: %s\nFile: %s \n Code: %s",
                $e->getMessage(),
                $e->getFile(),
                $e->getCode()
            );
        }

        $server->sendto($client_info['address'], $client_info['port'], $content);
    }

    /**
     * @param swoole_server $server
     * @param $data
     * @param $client_info
     * @return mixed
     */
    abstract public function doPacket(swoole_server $server, $data, $client_info);
}