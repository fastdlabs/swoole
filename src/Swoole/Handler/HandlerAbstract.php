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

use FastD\Swoole\Manager\Output;
use FastD\Swoole\Server\ServerInterface;
use FastD\Swoole\SwooleInterface;

/**
 * Class HandlerAbstract
 *
 * @package FastD\Swoole\Handler
 */
abstract class HandlerAbstract implements HandlerInterface
{
    use Output;

    /**
     * @var ServerInterface
     */
    protected $server;

    /**
     * @param SwooleInterface $swooleInterface
     * @return $this
     */
    public function handle(SwooleInterface $swooleInterface)
    {
        $this->server = $swooleInterface;

        $handles = get_class_methods($this);

        foreach ($handles as $value) {
            if ('on' == substr($value, 0, 2)) {
                $swooleInterface->on(lcfirst(substr($value, 2)), [$this, $value]);
            }
        }

        return $this;
    }

    /**
     * @param $name
     * @return void
     */
    public function rename($name)
    {
        try {
            if (function_exists('cli_set_process_title')) {
                cli_set_process_title($name);
            } else if (function_exists('swoole_set_process_name')) {
                swoole_set_process_name($name);
            }
        } catch (\Exception $e) {}
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

        $this->rename($this->server->getName() . ' master process(' . ')');

        $this->output("Server [Host: {$server->host} Port: {$server->port}](Pid: {$server->master_pid}) is startd...");
    }

    /**
     * Shutdown server process.
     */
    public function onShutdown()
    {
        $pid = $this->server->getPid();

        if (null !== ($file = $this->server->getPidFile())) {
            unlink($file);
        }

        $this->output("Server [Pid: {$pid}] is shutdown...");
    }
}