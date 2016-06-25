<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/18
 * Time: 上午11:15
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Server;

use FastD\Swoole\Console\Output;
use FastD\Swoole\Console\Process;

/**
 * Class ServerHandle
 *
 * @package FastD\Swoole\Server
 */
abstract class ServerCallbackHandle implements ServerCallbackInterface, ServerInterface
{
    /**
     * @param $server
     * @return $this
     */
    public function scan(\swoole_server $server)
    {
        $handles = get_class_methods($this);

        foreach ($handles as $value) {
            if ('on' == substr($value, 0, 2)) {
                $server->on(lcfirst(substr($value, 2)), [$this, $value]);
            }
        }

        return $this;
    }
    
    /**
     * Base start handle. Storage process id.
     *
     * @param \swoole_server $server
     * @return void
     */
    public function onStart(\swoole_server $server)
    {
        if (null !== ($file = $this->getPid())) {
            if (!is_dir($dir = dirname($file))) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($file, $server->master_pid . PHP_EOL);
        }

        Process::rename(static::SERVER_NAME . ' master');

        Output::output(sprintf('Server Master[%s] started', $server->master_pid));
    }

    /**
     * Shutdown server process.
     *
     * @param \swoole_server $server
     * @return void
     */
    public function onShutdown(\swoole_server $server)
    {
        if (null !== ($file = $this->getPid()) && !empty(trim(file_get_contents($file)))) {
            unlink($file);
        }

        Output::output(sprintf('Server Master[%s] shutdown ', $server->master_pid));
    }

    /**
     * @param \swoole_server $server
     *
     * @return void
     */
    public function onManagerStart(\swoole_server $server)
    {
        Process::rename(static::SERVER_NAME . ' manager');

        Output::output(sprintf('Server Manager[%s] started', $server->manager_pid));
    }

    /**
     * @param \swoole_server $server
     *
     * @return void
     */
    public function onManagerStop(\swoole_server $server)
    {
        Output::output(sprintf('Server Manager[%s] stop', $server->manager_pid));
    }

    /**
     * @param \swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStart(\swoole_server $server, int $worker_id)
    {
        Process::rename(static::SERVER_NAME . ' worker');

        Output::output(sprintf('Server Worker[%s] started [#%s]', $server->worker_pid, $worker_id));
    }

    /**
     * @param \swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStop(\swoole_server $server, int $worker_id)
    {
        Output::output(sprintf('Server Worker[%s] stop', $worker_id));
    }

    /**
     * @param \swoole_server $serv
     * @param int $worker_id
     * @param int $worker_pid
     * @param int $exit_code
     * @return void
     */
    public function onWorkerError(\swoole_server $serv, int $worker_id, int $worker_pid, int $exit_code)
    {
        Output::output(sprintf('Server Worker[%s] error', $worker_pid));
    }
}