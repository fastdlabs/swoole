<?php

declare(strict_types=1);

namespace FastD\Swoole\Listener\Server;

use FastD\Swoole\Event\Server\PacketEvent;
use FastD\Swoole\Listener\SwooleEventListener;
use Swoole\Server;

abstract class PacketListener extends ServerEventListener
{
    abstract public function onPacket(Server $server, string $data, array $client_info): void;

    public function listen(): iterable
    {
        return [
            PacketEvent::class
        ];
    }
}