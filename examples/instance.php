<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../vendor/autoload.php';

use FastD\Swoole\Server\Server;

class Test extends Server
{
    /**
     * @param \swoole_server $server
     * @param int $fd
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    public function doWork(\swoole_server $server, int $fd, int $from_id, string $data)
    {
        $server->send($fd, $data, $from_id);
        $server->close($fd);
    }

    /**
     * @param \swoole_server $server
     * @param string $data
     * @param array $client_info
     */
    public function doPacket(\swoole_server $server, string $data, array $client_info)
    {
        // TODO: Implement doPacket() method.
    }
}

$test = new Test();

$test->start();
