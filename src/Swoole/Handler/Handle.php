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

namespace FastD\Swoole\Handler;

use FastD\Swoole\Console\Output;
use FastD\Swoole\Console\Process;

/**
 * Class Handle
 *
 * @package FastD\Swoole\Handler
 */
class Handle extends HandlerAbstract
{
    /**
     * Base start handle. Storage process id.
     *
     * @param \swoole_server $server
     * @return void
     */
    public function onStart(\swoole_server $server)
    {
        if (null !== ($file = $this->server->getPid())) {
            if (!is_dir($dir = dirname($file))) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($file, $server->master_pid . PHP_EOL);
        }

        Process::rename($this->server->getProcessName() . ' master');

        Output::output(sprintf('Server Master[%s] started', $server->master_pid));
    }

    /**
     * Shutdown server process.
     *
     * @return void
     */
    public function onShutdown(\swoole_server $server)
    {
        if (null !== ($file = $this->server->getPid()) && !empty(trim(file_get_contents($file)))) {
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
        Process::rename($this->server->getProcessName() . ' manager');

        Output::output(sprintf('Server Manager[%s] started', $this->server->getPid()));
    }

    /**
     * @param \swoole_server $server
     *
     * @return void
     */
    public function onManagerStop(\swoole_server $server)
    {
        Output::output(sprintf('Server Manager[%s]' .
            ' stop', $server->manager_pid));
    }

    /**
     * @param \swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStart(\swoole_server $server, int $worker_id)
    {
        Process::rename($this->server->getProcessName() . ' worker');

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