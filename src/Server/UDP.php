<?php

declare(strict_types=1);

namespace FastD\Swoole\Server;

use FastD\Swoole\EventHandler\UDPEventInterface;
use Swoole\Server;

abstract class UDP extends Swoole implements UDPEventInterface
{
    protected string $protocol = 'udp';

    /**
     * @param Swoole $server
     * @param string $data
     * @param array $client_info
     */
    public function onPacket(Server $server, string $data, array $client_info): void
    {
        $server->sendto($client_info['address'], $client_info['port'], "Server ".$data);
    }

    public function createSwooleServer(string $protocol, string $host, int $port, int $mode, int $sockType): Server
    {
        return new Server($host, $port, $mode, $sockType);
    }
}
