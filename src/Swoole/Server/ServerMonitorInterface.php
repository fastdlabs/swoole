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
 * Interface ServerMonitor
 *
 * @package FastD\Swoole\Server
 */
interface ServerMonitorInterface
{
    /**
     * @param array $monitors
     * @return mixed
     */
    public function monitoring(array $monitors);

    /**
     * @param callable $callable
     * @return mixed
     */
    public function report(callable $callable);
}