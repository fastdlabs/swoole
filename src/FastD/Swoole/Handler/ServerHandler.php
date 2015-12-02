<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/11
 * Time: 上午10:12
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Handler;

use FastD\Swoole\Server\SwooleServer;
use FastD\Swoole\Server\SwooleServerInterface;

/**
 * Class SwooleHandler
 *
 * @package FastD\Swoole\Handler
 */
class ServerHandler implements ServerHandlerInterface
{
    /**
     * @var SwooleServer
     */
    protected $swoole;

    /**
     * @param SwooleServerInterface $swooleInterface
     * @return $this
     */
    public function handle(SwooleServerInterface $swooleInterface)
    {
        $this->swoole = $swooleInterface;

        foreach ($this->registerHandles() as $name => $callback) {
            $swooleInterface->on($name, $callback);
        }

        return $this;
    }

    /**
     * @param $name
     * @return void
     */
    public function rename($name)
    {
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($name);
        } else if (function_exists('swoole_set_process_name')) {
            swoole_set_process_name($name);
        }
    }

    /**
     * @param \swoole_server $server
     * @return mixed
     */
    public function onStart(\swoole_server $server)
    {
        if (null !== ($file = $this->swoole->getPidFile())) {
            if (!is_dir($dir = dirname($file))) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($file, $server->master_pid . PHP_EOL);
        }

        $this->rename($this->swoole->getContext()->hasGet('process_name', 'swoole') . ' master');
    }

    /**
     * @param \swoole_server $server
     * @return void
     */
    public function onShutdown(\swoole_server $server)
    {
        $file = $this->swoole->getPidFile();

        if (null !== $file && file_exists($file)) {
            @unlink($file);
        }

        $server->shutdown();
    }

    /**
     * @param \swoole_server $server
     * @return mixed
     */
    public function onManagerStart(\swoole_server $server)
    {
        $this->rename($this->swoole->getContext()->hasGet('process_name', 'swoole') . ' manager');
    }

    /**
     * @param \swoole_server $server
     * @param                $worker_id
     * @return mixed
     */
    public function onWorkerStart(\swoole_server $server, $worker_id)
    {
        $this->rename($this->swoole->getContext()->hasGet('process_name', 'swoole') . ' worker');
    }

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @return mixed
     */
    public function onConnect(\swoole_server $server, $fd, $from_id)
    {
        echo 'connection' . PHP_EOL;
    }

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @param                $data
     * @return mixed
     */
    public function onReceive(\swoole_server $server, $fd, $from_id, $data)
    {
        $server->send($fd, $data);
        $server->close($fd);
    }

    /**
     * @param \swoole_server $server
     * @param                $fd
     * @param                $from_id
     * @return mixed
     */
    public function onClose(\swoole_server $server, $fd, $from_id)
    {
        echo 'close';
    }

    /**
     * @return array
     */
    public function registerHandles()
    {
        return [
            'Start'     => [$this, 'onStart'],
            'Shutdown'  => [$this, 'onShutdown'],
            'Connect'   => [$this, 'onConnect'],
            'Receive'   => [$this, 'onReceive'],
            'Close'     => [$this, 'onClose'],
        ];
    }
}