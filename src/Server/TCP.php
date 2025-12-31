<?php

declare(strict_types=1);

namespace FastD\Swoole\Server;

use FastD\Swoole\EventHandler\TCPEventInterface;
use Swoole\Server;

abstract class TCP extends Swoole implements TCPEventInterface
{
    public function onReceive(Server $server, int $fd, int $reactorId, string $data): bool
    {
        $server->send($fd, $data);
    }

    public function createSwooleServer(string $protocol, string $host, int $port, int $mode, int $sockType): Server
    {
        return new Server($host, $port, $mode, $sockType);
    }
}
