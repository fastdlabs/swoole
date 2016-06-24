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
 * Interface TaskInterface
 *
 * @package FastD\Swoole\Worker
 */
interface TaskInterface
{
    /**
     * @param \swoole_server $server
     * @param int $task_id
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    public function doTask(\swoole_server $server, int $task_id, int $from_id, string $data);
}