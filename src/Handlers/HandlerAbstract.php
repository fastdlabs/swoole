<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Handlers;


use FastD\Swoole\Server\ServerAbstract;

/**
 * Class Handler
 * @package FastD\Swoole\Handlers
 */
abstract class HandlerAbstract
{
    protected ServerAbstract $server;

    public function __construct(ServerAbstract $server)
    {
        $this->server = $server;
    }

    /**
     * @param Server $server
     * @return bool
     */
    abstract public function onStart(Server $server): bool;

    /**
     * @param Server $server
     * @return bool
     */
    abstract public function onShutdown(Server $server): bool;

    /**
     * @param Server $server
     * @return bool
     */
    abstract public function onManagerStart(Server $server): bool;

    /**
     * @param Server $server
     * @return bool
     */
    abstract public function onManagerStop(Server $server): bool;

    /**
     * @param Server $server
     * @param int $id
     * @return bool
     */
    abstract public function onWorkerStart(Server $server, int $id): bool;

    /**
     * @param Server $server
     * @param int $id
     * @return bool
     */
    abstract public function onWorkerStop(Server $server, int $id): bool;

    /**
     * @param Server $server
     * @param int $worker_id
     * @param int $worker_pid
     * @param int $exit_code
     * @param int $signal
     * @return bool
     */
    abstract public function onWorkerError(Server $server, int $worker_id, int $worker_pid, int $exit_code, int $signal): bool;

    /**
     * @param Server $server
     * @param int $id
     * @return bool
     */
    abstract public function onWorkerExit(Server $server, int $id): bool;

    /**
     * @param Server $server
     * @param int $fd
     * @param int $id
     * @return bool
     */
    abstract public function onClose(Server $server, int $fd, int $id): bool;

    /**
     * @param Swoole\Server $server
     * @param int $src_worker_id
     * @param $message
     * @return bool
     */
    abstract public function onPipeMessage(Server $server, int $src_worker_id, $message): bool;
}
