<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https: //www.github.com/fastdlabs
 * @see      HTTPServer: //www.fastdlabs.com/
 */

namespace FastD\Swoole\Handlers;


use swoole_server;

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
    public function onStart(swoole_server $server): void;

    /**
     * Shutdown server process.
     *
     * @param swoole_server $server
     * @return void
     */
    public function onShutdown(swoole_server $server): void;

    /**
     * @param swoole_server $server
     *
     * @return void
     */
    public function onManagerStart(swoole_server $server): void;

    /**
     * @param swoole_server $server
     *
     * @return void
     */
    public function onManagerStop(swoole_server $server): void;

    /**
     * @param swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStart(swoole_server $server, int $worker_id): void;
    /**
     * @param swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStop(swoole_server $server, int $worker_id): void;

    /**
     * @param swoole_server $server
     * @param $workerId
     * @param $workerPid
     * @param $code
     */
    public function onWorkerError(swoole_server $server, int $workerId, int $workerPid, int $code): void;

    /**
     * @param swoole_server $server
     * @param $fd
     * @param $from_id
     */
    public function onConnect(swoole_server $server, int $fd, int $from_id): void;
    
    /**
     * @param swoole_server $server
     * @param $fd
     * @param $fromId
     */
    public function onClose(swoole_server $server, int $fd, int $fromId): void;

    /**
     * @param swoole_server $server
     * @param int $src_worker_id
     * @param string $message
     */
    public function onPipeMessage(swoole_server $server, int $src_worker_id, string $message): void;
}