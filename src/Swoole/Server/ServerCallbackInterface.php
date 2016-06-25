<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;

/**
 * Interface ServerCallbackInterface
 *
 * @package FastD\Swoole\Server
 */
interface ServerCallbackInterface
{
    /**
     * Base start handle. Storage process id.
     *
     * @param \swoole_server $server
     * @return void
     */
    public function onStart(\swoole_server $server);

    /**
     * Shutdown server process.
     *
     * @param \swoole_server $server
     * @return void
     */
    public function onShutdown(\swoole_server $server);

    /**
     * @param \swoole_server $server
     *
     * @return void
     */
    public function onManagerStart(\swoole_server $server);

    /**
     * @param \swoole_server $server
     *
     * @return void
     */
    public function onManagerStop(\swoole_server $server);

    /**
     * @param \swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStart(\swoole_server $server, int $worker_id);

    /**
     * @param \swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStop(\swoole_server $server, int $worker_id);

    /**
     * @param \swoole_server $serv
     * @param int $worker_id
     * @param int $worker_pid
     * @param int $exit_code
     * @return void
     */
    public function onWorkerError(\swoole_server $serv, int $worker_id, int $worker_pid, int $exit_code);
}