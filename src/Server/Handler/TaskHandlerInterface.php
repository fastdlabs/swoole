<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Server\Handler;


use Swoole\Server;

/**
 * Interface TaskServerHandlerInterface
 * @package FastD\Swoole\Handlers
 */
interface TaskHandlerInterface
{
    /**
     * @param Server $server
     * @param $taskId
     * @param $workerId
     * @param $data
     * @return mixed
     */
    public function onTask(Server $server, int $taskId, int $workerId, string $data):  void;

    /**
     * @param Server $server
     * @param $taskId
     * @param $data
     * @return mixed
     */
    public function onFinish(Server $server, int $taskId, string $data): void;
}
