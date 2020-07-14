<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;

use FastD\Swoole\Handlers\TCPHandlerInterface;
use Swoole\Server;

/**
 * Class TCPServer
 * @package FastD\Swoole
 */
class TCPServer extends ServerAbstract implements TCPHandlerInterface
{
    /**
     * @param Server $server
     * @return bool
     */
    public function onReceive(Server $server, int $fd, int $reactorId, string $data): bool
    {
        output(sprintf('Receive: {%s}', $data));
        $server->send($fd, $data);
    }
}
