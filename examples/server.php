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
     * @param $fd
     * @param $from_id
     */
    public function onConnect(swoole_server $server, int $fd, int $from_id): void
    {
        // TODO: Implement onConnect() method.
    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param $fromId
     */
    public function onClose(swoole_server $server, int $fd, int $fromId): void
    {
        // TODO: Implement onClose() method.
    }

    /**
     * @param swoole_server $server
     * @param int $fd
     * @param int $reactor_id
     * @param string $data
     */
    public function onReceive(swoole_server $server, int $fd, int $reactor_id, string $data): void
    {
        // TODO: Implement onReceive() method.
    }

    /**
     * @param swoole_server $server
     * @param string $data
     * @param array $client_info
     */
    public function onPacket(swoole_server $server, string $data, array $client_info): void
    {
        // TODO: Implement onPacket() method.
    }

    /**
     * @param swoole_server $server
     * @param int $src_worker_id
     * @param string $message
     */
    public function onPipeMessage(swoole_server $server, int $src_worker_id, string $message): void
    {
        // TODO: Implement onPipeMessage() method.
    }

    /**
     * @param swoole_server $server
     * @param $taskId
     * @param $workerId
     * @param $data
     * @return mixed
     */
    public function onTask(swoole_server $server, int $taskId, int $workerId, string $data): void
    {
        // TODO: Implement onTask() method.
    }

    /**
     * @param swoole_server $server
     * @param $taskId
     * @param $data
     * @return mixed
     */
    public function onFinish(swoole_server $server, int $taskId, string $data): void
    {
        // TODO: Implement onFinish() method.
    }
}

BaseServer::createServer()->start();
