<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Tools;

use swoole_server;

/**
 * Trait PresetCallback
 *
 * @package FastD\Swoole\Console
 */
trait PresetCallback
{
    abstract public function rename($name);

    abstract public function output($msg);

    /**
     * Base start handle. Storage process id.
     *
     * @param swoole_server $server
     * @return void
     */
    public function onStart(swoole_server $server)
    {
        if (null !== ($file = $this->getPid())) {
            if (!is_dir($dir = dirname($file))) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($file, $server->master_pid . PHP_EOL);
        }

        $this->rename(static::SERVER_NAME . ' master');

        $this->output(sprintf("Server %s://%s:%s", $this->getServerType(), $this->getHost(), $this->getPort()));
        foreach ($this->ports as $port) {
            $this->output(sprintf("➜ Listen %s://%s:%s", $this->getServerType($port->type), $port->host, $port->port));
        }
        $this->output(sprintf('Server Master[#%s] is started', $server->master_pid));
    }

    /**
     * Shutdown server process.
     *
     * @param swoole_server $server
     * @return void
     */
    public function onShutdown(swoole_server $server)
    {
        if (null !== ($file = $this->getPid()) && !empty(trim(file_get_contents($file)))) {
            unlink($file);
        }

        $this->output(sprintf('Server Master[#%s] is shutdown ', $server->master_pid));
    }

    /**
     * @param swoole_server $server
     *
     * @return void
     */
    public function onManagerStart(swoole_server $server)
    {
        $this->rename(static::SERVER_NAME . ' manager');

        $this->output(sprintf('Server Manager[#%s] is started', $server->manager_pid));
    }

    /**
     * @param swoole_server $server
     *
     * @return void
     */
    public function onManagerStop(swoole_server $server)
    {
        $this->output(sprintf('Server Manager[#%s] is shutdown.', $server->manager_pid));
    }

    /**
     * @param swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStart(swoole_server $server, int $worker_id)
    {
        $this->rename(static::SERVER_NAME . ' worker');

        $this->output(sprintf('Server Worker[#%s] is started [#%s]', $server->worker_pid, $worker_id));
    }

    /**
     * @param swoole_server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStop(swoole_server $server, int $worker_id)
    {
        $this->output(sprintf('Server Worker[#%s] is shutdown', $worker_id));
    }

    /**
     * @param swoole_server $server
     * @param int $worker_id
     * @param int $worker_pid
     * @param int $exit_code
     * @return void
     */
    public function onWorkerError(swoole_server $server, int $worker_id, int $worker_pid, int $exit_code)
    {
        $this->output(sprintf('Server Worker[#%s] error. Exit code: [%s]', $worker_pid, $exit_code));
    }
}