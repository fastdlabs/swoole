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

use FastD\Swoole\Server\ServerHandler;
use FastD\Swoole\Server\ServerInterface;

/**
 * Class Swoole
 *
 * @package FastD\Swoole
 */
class Swoole implements ServerInterface
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

    /**
     * The last time start swoole server.
     *
     * @var \stdClass|\swoole_server
     */
    protected $lastSwoole;

    /**
     * @param Context $context
     * @param         $mode
     * @param         $sockType
     */
    public function __construct(Context $context, $mode = SWOOLE_PROCESS, $sockType = SWOOLE_SOCK_TCP)
    {
        if (null !== ($sock = $context->get('sock_file'))) {
            if (file_exists($sock)) {
                $this->lastSwoole = new \stdClass();
                $this->lastSwoole->pid = (int)file_get_contents($sock);
                unset($sock);
            }
        }

        $this->server = new \swoole_server($context->getScheme(), $context->getPort(), $mode, $sockType);

        $this->context = $context;
    }

    /**
     * @param                             $protocol
     * @param array                       $config
     * @param SwooleHandlerInterface|null $swooleHandlerInterface
     * @return static
     */
    public static function create($protocol, array $config = null, SwooleHandlerInterface $swooleHandlerInterface = null)
    {
        $swoole = new static(new Context($protocol, $config));

        if (null !== $swooleHandlerInterface) {
            $swoole->handle($swooleHandlerInterface);
        }

        return $swoole;
    }

    /**
     * @return \stdClass|\swoole_server
     */
    public function getLastSwoole()
    {
        return $this->lastSwoole;
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
     * @return array|null
     */
    public function status()
    {
        return $this->lastSwoole->server->stats();
    }

    /**
     * @return mixed
     */
    public function start()
    {
        $this->server->set($this->context->all());

        if (null === $this->handler) {
            $this->handler = new ServerHandler([
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
     * @return mixed
     */
    public function stop()
    {
        return $this->lastSwoole->server->shutdown();
    }

    /**
     * @return mixed
     */
    public function reload()
    {
        return $this->lastSwoole->server->reload();
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
}