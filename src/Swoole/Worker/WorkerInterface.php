<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Worker;

/**
 * Interface WorkerInterface
 *
 * @package FastD\Swoole\Worker
 */
interface WorkerInterface
{
    /**
     * @param \swoole_server $server
     * @param $fd
     * @param $from_id
     * @param $data
     * @return mixed
     */
    public function doWork(\swoole_server $server, int $fd, int $from_id, string $data);
}