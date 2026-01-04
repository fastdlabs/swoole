<?php

declare(strict_types=1);

namespace FastD\Swoole\Server;

use FastD\Swoole\EventHandler\UDPEventInterface;
use Swoole\Server;

abstract class UDP extends Swoole implements UDPEventInterface
{
    public function onPacket(Server $server, string $data, array $client_info): void
    {
        $server->sendto($client_info['address'], $client_info['port'], "Server ".$data);
    }
}
