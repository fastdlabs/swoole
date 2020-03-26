<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Handlers;


use Swoole\Server;

/**
 * Interface HandlerInterface
 * @package FastD\Swoole\Handlers
 */
interface HandlerInterface
{
    /**
     * @param Server $server
     * @return bool
     */
    public function onStart(Server $server): bool;

    /**
     * @param Server $server
     * @return bool
     */
    public function onShutdown(Server $server): bool;

    /**
     * @param Server $server
     * @return bool
     */
    public function onManagerStart(Server $server): bool;

    /**
     * @param Server $server
     * @return bool
     */
    public function onManagerStop(Server $server): bool;

    /**
     * @param Server $server
     * @param int $id
     * @return bool
     */
    public function onWorkerStart(Server $server, int $id): bool;

    /**
     * @param Server $server
     * @param int $id
     * @return bool
     */
    public function onWorkerStop(Server $server, int $id): bool;

    /**
     * @param Server $server
     * @param int $worker_id
     * @param int $worker_pid
     * @param int $exit_code
     * @param int $signal
     * @return bool
     */
    public function onWorkerError(Server $server, int $worker_id, int $worker_pid, int $exit_code, int $signal): bool;

    /**
     * @param Server $server
     * @param int $id
     * @return bool
     */
    public function onWorkerExit(Server $server, int $id): bool;

    /**
     * @param Server $server
     * @param int $fd
     * @param int $id
     * @return bool
     */
    public function onClose(Server $server, int $fd, int $id): bool;

    /**
     * @param Swoole\Server $server
     * @param int $src_worker_id
     * @param $message
     * @return bool
     */
    public function onPipeMessage(Server $server, int $src_worker_id, $message): bool;
}