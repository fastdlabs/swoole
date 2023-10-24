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
 * Interface UDPServerHandlerInterface
 * @package FastD\Swoole\Handlers
 */
interface UDPHandlerInterface
{
    /**
     * @param Server $server
     * @param string $data
     * @param array $client_info
     */
    public function onPacket(Server $server, string $data, array $client_info): void;
}
