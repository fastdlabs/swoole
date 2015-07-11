<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/9
 * Time: 下午6:23
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole;

class Swoole implements SwooleInterface
{
    /**
     * @var \swoole_server
     */
    protected $server;

    protected $context;

    protected $prepareBind = [
        'start' => 'onStart',
        'shutdown' => 'onShutdown',
        'workerStart' => 'onWorkerStart',
        'workerStop' => 'onWorkerStop',
        'timer' => 'onTimer',
        'connect' => 'onConnect',
        'receive' => 'onReceive',
        'packet' => 'onPacket',
        'close' => 'onClose',
        'task' => 'onTask',
        'finish' => 'onFinish',
        'pipeMessage' => 'onPipeMessage',
        'workerError' => 'onWorkerError',
        'managerStart' => 'onManagerStart',
        'managerStop' => 'onManagerStop',
    ];

    protected $pid_file = './run/swoole.pid';

    public function __construct(Context $context, $mode = SWOOLE_PROCESS, $sockType = SWOOLE_SOCK_TCP)
    {
        $this->server = new \swoole_server($context->getScheme(), $context->getPort(), $mode, $sockType);

        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getPidFile()
    {
        return $this->pid_file;
    }

    /**
     * @param string $pid_file
     * @return $this
     */
    public function setPidFile($pid_file)
    {
        $this->pid_file = $pid_file;
        return $this;
    }

    public static function create($protocol, array $config = [])
    {
        return new static(new Context($protocol, $config));
    }

    public function run()
    {
        $this->server->set($this->context->all());

        foreach ($this->prepareBind as $name => $callback) {
            $this->server->on($name, [$this, $callback]);
        }

        $this->server->start();
    }

    public function daemonize()
    {
        $this->context->set('daemonize', true);

        return $this;
    }

    public function onStart(\swoole_server $server)
    {
        $dir = dirname($this->getPidFile());
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($server->pid, $this->getPidFile());
    }

    public function status()
    {
        // TODO: Implement status() method.
    }

    public function start()
    {
        // TODO: Implement start() method.
    }

    public function stop()
    {
        // TODO: Implement stop() method.
    }

    public function reload()
    {
        // TODO: Implement reload() method.
    }

    public function handle(SwooleHandlerInterface $swooleHandlerInterface = null, array $on = ['onStart', 'onStop'])
    {
        // TODO: Implement handle() method.
    }
}