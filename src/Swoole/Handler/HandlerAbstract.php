<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/1/29
 * Time: 下午11:30
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Handler;

use FastD\Swoole\Console\Output;
use FastD\Swoole\Server\Server;

/**
 * Class HandlerAbstract
 *
 * @package FastD\Swoole\Handler
 */
abstract class HandlerAbstract implements HandlerInterface
{
    /**
     * @var Server
     */
    protected $server;

    public function handle(Server $server)
    {
        $this->server = $server;

        $handles = get_class_methods($this);

        foreach ($handles as $value) {
            if ('on' == substr($value, 0, 2)) {
                $server->on(lcfirst(substr($value, 2)), [$this, $value]);
            }
        }

        return $this;
    }

    /**
     * Base start handle. Storage process id.
     *
     * @param \swoole_server $server
     * @return mixed
     */
    public function onStart(\swoole_server $server)
    {
        if (null !== ($file = $this->server->getPidFile())) {
            if (!is_dir($dir = dirname($file))) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($file, $server->master_pid . PHP_EOL);
        }

        Output::output(sprintf('server [%s] started', Server::SERVER_NAME));
    }

    /**
     * Shutdown server process.
     */
    public function onShutdown()
    {
        $pid = @file_get_contents($this->server->getPidFile());

        if (null !== ($file = $this->server->getPidFile())) {
            unlink($file);
        }

        Output::output(sprintf('shutdown server [%s]', $pid));
    }
}