<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

use FastD\Swoole\Server\TCP;

include __DIR__ . '/../vendor/autoload.php';

class BaseServer extends \FastD\Swoole\Server
{
    /**
     * @param swoole_server $server
     * @param $data
     * @param $taskId
     * @param $workerId
     * @return mixed
     */
    public function doTask(swoole_server $server, $data, $taskId, $workerId)
    {
        // TODO: Implement doTask() method.
    }

    /**
     * @param swoole_server $server
     * @param $data
     * @param $taskId
     * @return mixed
     */
    public function doFinish(swoole_server $server, $data, $taskId)
    {
        // TODO: Implement doFinish() method.
    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param $from_id
     */
    public function doConnect(swoole_server $server, $fd, $from_id)
    {
        // TODO: Implement doConnect() method.
    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param $fromId
     */
    public function doClose(swoole_server $server, $fd, $fromId)
    {
        // TODO: Implement doClose() method.
    }
}

BaseServer::createServer();
