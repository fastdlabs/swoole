<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

include __DIR__ . '/../../vendor/autoload.php';

use Swoole\Server;

class UnixServer extends \FastD\Swoole\UnixServer
{
    /**
     * @param Server $server
     * @param $fd
     * @param $from_id
     */
    public function onConnect(Server $server, int $fd, int $from_id): void
    {
        // TODO: Implement onConnect() method.
    }

    /**
     * @param Server $server
     * @param $fd
     * @param $fromId
     */
    public function onClose(Server $server, int $fd, int $fromId): void
    {
        // TODO: Implement onClose() method.
    }

    /**
     * @param Server $server
     * @param int $src_worker_id
     * @param string $message
     */
    public function onPipeMessage(Server $server, int $src_worker_id, string $message): void
    {
        // TODO: Implement onPipeMessage() method.
    }
}
