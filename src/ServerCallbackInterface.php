<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https: //www.github.com/fastdlabs
 * @see      HTTPServer: //www.fastdlabs.com/
 */

namespace FastD\Swoole;

use swoole_server;


/**
 * Interface ServerCallbackInterface
 * @package FastD\Swoole\Server
 */
interface ServerCallbackInterface
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
     * @param int $fd
     * @param int $reactor_id
     * @param string $data
     */
    public function onReceive(swoole_server $server, int $fd, int $reactor_id, string $data): void;

    /**
     * @param swoole_server $server
     * @param string $data
     * @param array $client_info
     */
    public function onPacket(swoole_server $server, string $data, array $client_info): void;

    /**
     * @param swoole_server $server
     * @param int $src_worker_id
     * @param string $message
     */
    public function onPipeMessage(swoole_server $server, int $src_worker_id, string $message): void;

    /**
     * @param swoole_server $server
     * @param $taskId
     * @param $workerId
     * @param $data
     * @return mixed
     */
    public function onTask(swoole_server $server, int $taskId, int $workerId, string $data):  void;

    /**
     * @param swoole_server $server
     * @param $taskId
     * @param $data
     * @return mixed
     */
    public function onFinish(swoole_server $server, int $taskId, string $data): void;

}