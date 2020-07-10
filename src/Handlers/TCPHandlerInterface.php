<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Handlers;


use Swoole\Server;

/**
 * Interface TCPServerCallbackInterface
 * @package FastD\Swoole\Handlers
 */
interface TCPHandlerInterface extends HandlerInterface
{
    /**
     * @param Server $server
     * @param int $fd
     * @param int $reactor_id
     * @param string $data
     */
    public function onReceive(Server $server, int $fd, int $reactor_id, string $data): void;
}
