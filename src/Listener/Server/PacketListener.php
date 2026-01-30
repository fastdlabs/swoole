<?php

declare(strict_types=1);

namespace FastD\Swoole\Server\Listener;

use FastD\Swoole\Server\Event\PacketEvent;
use FastD\Swoole\Server\UDP;
use Swoole\Server;

abstract class PacketListener extends SwooleEventListener
{
    abstract public function onPacket(Server $server, string $data, array $client_info): void;

    public function listen(): iterable
    {
        return [
            PacketEvent::class
        ];
    }
}