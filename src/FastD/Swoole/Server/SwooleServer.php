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
use FastD\Swoole\Handler\ServerHandlerInterface;
use FastD\Swoole\Handler\SwooleHandlerInterface;

/**
 * Class Swoole
 *
 * @package FastD\Swoole\Server
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

    /**
     * SwooleServer constructor.
     *
     * @param                             $protocol
     * @param array                       $config
     * @param ServerHandlerInterface|null $serverHandlerInterface
     */
    public function __construct($protocol, array $config = [], ServerHandlerInterface $serverHandlerInterface = null)
    {
        $this->context = new Context($protocol, $config);

        $this->server = new \swoole_server(
            $this->context->getScheme(),
            $this->context->getPort(),
            SWOOLE_PROCESS,
            SWOOLE_SOCK_TCP
        );

        if (null !== $serverHandlerInterface) {
            $this->handle($serverHandlerInterface);
        }
    }

    /**
     * @param       $protocol
     * @param array $config
     * @return static
     */
    public static function create($protocol, array $config = [], ServerHandlerInterface $serverHandlerInterface = null)
    {
        return new static($protocol, $config, $serverHandlerInterface);
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

    /**
     * Get server pid file absolute path.
     *
     * @return string
     */
    public function getPidFile()
    {
        return $this->context->has('pid_file') ? $this->context->get('pid_file') : false;
    }

    /**
     * @return int|null
     */
    public function getPid()
    {
        $file = $this->getPidFile();

        return false !== $file ? (int)file_get_contents($file) : null;
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
            throw new \RuntimeException("Server is not has handler.");
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
     * @param      $callback
     * @return $this
     */
    public function on($name, $callback)
    {
        $this->server->on($name, $callback);

        return $this;
    }

    /**
     * @param $name
     * @return null
     */
    public function getConfig($name)
    {
        return $this->context->get($name);
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
        return $this->getConfig('user');
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
        return $this->getConfig('group');
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
     * @return array|null
     */
    public function status()
    {
        $pid = $this->getPid();
        if (empty($pid)) {
            echo 'Server [' . $this->getContext()->get('process_name') . '] not running...' . PHP_EOL;
            return 0;
        }
        echo 'Server [' . $this->getContext()->get('process_name') . ' pid: ' . $pid . '] is running...' . PHP_EOL;
        return 0;
    }

    /**
     * @return mixed
     */
    public function shutdown()
    {
        $pid = $this->getPid();

        if (empty($pid)) {
            echo 'Server [' . $this->getContext()->get('process_name') . '] not running...' . PHP_EOL;
            return 1;
        }

        exec("kill -15 {$pid}");
        echo 'Server [' . $this->getContext()->get('process_name') . ' pid: ' . $pid . '] is stop...' . PHP_EOL;
        return 0;
    }

    /**
     * @return mixed
     */
    public function reload()
    {
        $pid = $this->getPid();

        if (empty($pid)) {
            echo 'Server [' . $this->getContext()->get('process_name') . '] not running...' . PHP_EOL;
        }
        exec("kill -USR1 {$pid}");
        echo 'Server [' . $this->getContext()->get('process_name') . ' pid: ' . $pid . '] reload...' . PHP_EOL;

        return 0;
    }

    /**
     * @return int
     */
    public function usage()
    {
        echo 'Usage: Server {start|stop|restart|reload|status} ' . PHP_EOL;
        return 0;
    }
}