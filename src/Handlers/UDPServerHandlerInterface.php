<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Handlers;


use swoole_server;

/**
 * Interface UDPServerHandlerInterface
 * @package FastD\Swoole\Handlers
 */
interface UDPServerHandlerInterface
{
    /**
     * @param swoole_server $server
     * @param string $data
     * @param array $client_info
     */
    public function onPacket(swoole_server $server, string $data, array $client_info): void;
}