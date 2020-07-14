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
use Swoole\Server;

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
     * @return void
     */
    public function onStart(Server $server): void
    {
        output(sprintf('Server Started, pid: [%d]', $server->master_pid));
    }

    /**
     * @param Server $server
     * @return void
     */
    public function onShutdown(Server $server): void
    {
        output(sprintf('Server shutdown, pid: [%d]', $server->master_pid));
    }

    /**
     * @param Server $server
     * @return void
     */
    public function onManagerStart(Server $server): void
    {
        output(sprintf('Server manager started, pid: [%d]', $server->manager_pid));
    }

    /**
     * @param Server $server
     * @return void
     */
    public function onManagerStop(Server $server): void
    {
        output(sprintf('Server manager stop, pid: [%d]', $server->manager_pid));
    }

    /**
     * @param Server $server
     * @param int $id
     * @return void
     */
    public function onWorkerStart(Server $server, int $id): void
    {
        output(sprintf('Server worker[%d] started', $id));
    }

    /**
     * @param Server $server
     * @param int $id
     */
    public function onWorkerStop(Server $server, int $id): void
    {
        output(sprintf('Server worker[%d] stop', $id));
    }

    /**
     * @param Server $server
     * @param int $id
     * @param int $worker_pid
     * @param int $exit_code
     * @param int $signal
     */
    public function onWorkerError(Server $server, int $id, int $worker_pid, int $exit_code, int $signal): void
    {
        output(sprintf('Server worker[%d] error, error code [%d], signal [%d]', $worker_pid, $exit_code, $signal));
    }

    /**
     * @param Server $server
     * @param int $id
     */
    public function onWorkerExit(Server $server, int $id): void
    {
        output(sprintf('Server worker[%d] exit', $id));
    }

    /**
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose(Server $server, int $fd, int $reactorId): void
    {
        output(sprintf('[%d] close', $fd));
    }
}
