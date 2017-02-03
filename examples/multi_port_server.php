<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../vendor/autoload.php';

class Server extends \FastD\Swoole\Server\Tcp
{
    /**
     * @param swoole_server $server
     * @param $fd
     * @param $data
     * @param $from_id
     * @return mixed
     */
    public function doWork(swoole_server $server, $fd, $data, $from_id)
    {
        return 'hello server1';
    }

    /**
     * Please return swoole configuration array.
     *
     * @return array
     */
    public function configure()
    {
        // TODO: Implement configure() method.
    }
}

class Server2 extends \FastD\Swoole\Server\Tcp
{
    /**
     * @param swoole_server $server
     * @param $fd
     * @param $data
     * @param $from_id
     * @return mixed
     */
    public function doWork(swoole_server $server, $fd, $data, $from_id)
    {
        return 'hello server2';
    }

    /**
     * Please return swoole configuration array.
     *
     * @return array
     */
    public function configure()
    {
        // TODO: Implement configure() method.
    }
}

$server = new Server('tcp server', 'tcp://127.0.0.1:9527');

$server->listen(new Server2('tcp server2', 'tcp://127.0.0.1:9528'));

$server->start();

