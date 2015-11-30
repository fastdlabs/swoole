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

namespace FastD\Swoole\Server;

use FastD\Swoole\Context;
use FastD\Swoole\Handler\SwooleHandlerInterface;

/**
 * Class Swoole
 *
 * @package FastD\Swoole
 */
class SwooleServer implements SwooleServerInterface
{
    /**
     * @var \swoole_server
     */
    protected $server;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var SwooleHandlerInterface
     */
    protected $handler;

    protected $pid;

    public function __construct($protocol, array $config = [])
    {
        $this->context = new Context($protocol, $config);

        print_r($this->context);die;

        $this->server = new \swoole_server($context->getScheme(), $context->getPort(), SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
    }

    public static function create($protocol, array $config = [])
    {
        return new static($protocol, $config);
    }

    /**
     * @param Context $context
     * @return void
     */
    public function initPid(Context $context)
    {
        if (null !== ($sock = $context->get('pid_file'))) {
            if (file_exists($sock)) {
                $this->pid = file_get_contents($sock);
                unset($sock);
            }
        }
    }

    public function getPid()
    {
        return (int)$this->pid;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param Context $context
     * @return $this
     */
    public function setContext(Context $context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return $this
     */
    public function daemonize()
    {
        $this->context->set('daemonize', true);

        return $this;
    }

    /**
     * @return mixed
     */
    public function start()
    {
        $this->server->set($this->context->all());

        if (null === $this->handler) {
            $this->handler = new TcpHandler([
                'start',
                'shutdown',
                'workerStart',
                'workerStop',
                'timer',
                'connect',
                'receive',
                'packet',
                'close',
                'task',
                'finish',
                'pipeMessage',
                'workerError',
                'managerStart',
                'managerStop',
            ]);
        }

        $this->handler->handle($this);

        return $this->server->start();
    }

    /**
     * @param SwooleHandlerInterface $swooleHandlerInterface
     * @return $this
     */
    public function handle(SwooleHandlerInterface $swooleHandlerInterface)
    {
        $this->handler = $swooleHandlerInterface;

        return $this;
    }

    /**
     * @param      $name
     * @param null $callback
     * @return $this
     */
    public function on($name, $callback = null)
    {
        $this->server->on($name, $callback);

        return $this;
    }

    /**
     * @param      $name
     * @param null $value
     * @return $this
     */
    public function setConfig($name, $value = null)
    {
        $this->context->set($name, $value);

        return $this;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->context->get('user');
    }

    /**
     * @param $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->setConfig('user', $user);

        return $this;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->context->get('group');
    }

    /**
     * @param $group
     * @return $this
     */
    public function setGroup($group)
    {
        $this->setConfig('group', $group);

        return $this;
    }

    /**
     * @param $master_name
     * @return $this
     */
    public function rename($master_name)
    {
        $this->setConfig('process_name', $master_name);

        return $this;
    }

    /**
     * Get server pid file absolute path.
     *
     * @return string
     */
    public function getPidPath()
    {
        // TODO: Implement getPidPath() method.
    }

    /**
     * Get server running status.
     *
     * @return string
     */
    public function status()
    {
        // TODO: Implement status() method.
    }

    /**
     * Shutdown running server.
     *
     * @return int
     */
    public function shutdown()
    {
        // TODO: Implement shutdown() method.
    }
}