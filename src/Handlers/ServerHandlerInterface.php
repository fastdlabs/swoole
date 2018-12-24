<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https: //www.github.com/fastdlabs
 * @see      HTTPServer: //www.fastdlabs.com/
 */

namespace FastD\Swoole\Handlers;


use Swoole\Server;

/**
 * Interface ServerHandlerInterface
 * @package FastD\Swoole\Handlers
 */
interface ServerHandlerInterface
{
    /**
     * Base start handle. Storage process id.
     *
     * @param swoole_server $server
     * @return void
     */
    public function onStart(Server $server): void;

    /**
     * Shutdown server process.
     *
     * @param Server $server
     * @return void
     */
    public function onShutdown(Server $server): void;

    /**
     * @param Server $server
     *
     * @return void
     */
    public function onManagerStart(Server $server): void;

    /**
     * @param Server $server
     *
     * @return void
     */
    public function onManagerStop(Server $server): void;

    /**
     * @param Server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStart(Server $server, int $worker_id): void;
    /**
     * @param Server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStop(Server $server, int $worker_id): void;

    /**
     * @param Server $server
     * @param $workerId
     * @param $workerPid
     * @param $code
     */
    public function onWorkerError(Server $server, int $workerId, int $workerPid, int $code): void;

    /**
     * @param Server $server
     * @param $fd
     * @param $from_id
     */
    public function onConnect(Server $server, int $fd, int $from_id): void;
    
    /**
     * @param Server $server
     * @param $fd
     * @param $fromId
     */
    public function onClose(Server $server, int $fd, int $fromId): void;

    /**
     * @param Server $server
     * @param int $src_worker_id
     * @param string $message
     */
    public function onPipeMessage(Server $server, int $src_worker_id, string $message): void;
}