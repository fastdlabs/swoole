<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Server\Handler;


use Swoole\Server;

/**
 * Interface TCPServerCallbackInterface
 * @package FastD\Swoole\Handlers
 */
interface TCPHandlerInterface
{
    /**
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     * @param string $data
     * @return bool
     */
    public function onReceive(Server $server, int $fd, int $reactorId, string $data): bool;
}
