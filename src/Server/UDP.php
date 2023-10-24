<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;

use FastD\Swoole\Server\Handler\UDPHandlerInterface;
use Swoole\Server;


/**
 * Class UDPServer
 * @package FastD\Swoole
 */
class UDP extends AbstractServer implements UDPHandlerInterface
{
    protected string $protocol = 'udp';

    /**
     * @param Server $server
     * @param string $data
     * @param array $client_info
     */
    public function onPacket(Server $server, string $data, array $client_info): void
    {
        output(sprintf('Client [%s] port [%s], Receive: %s', $client_info['address'], $client_info['port'], $data));
        $server->sendto($client_info['address'], $client_info['port'], "Server ".$data);
    }
}
