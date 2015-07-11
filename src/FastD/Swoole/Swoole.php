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

/**
 * Class Swoole
 *
 * @package FastD\Swoole
 */
class Swoole implements SwooleInterface
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
     * @param Context $context
     * @param         $mode
     * @param         $sockType
     */
    public function __construct(Context $context, $mode = SWOOLE_PROCESS, $sockType = SWOOLE_SOCK_TCP)
    {
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

    public function status()
    {
        // TODO: Implement status() method.
    }

    public function start()
    {
        $this->server->set($this->context->all());

        $this->handler->handle($this);

        return $this->server->start();
    }

    public function stop()
    {
        // TODO: Implement stop() method.
    }

    public function reload()
    {
        // TODO: Implement reload() method.
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

    public function on($name, $callback = null)
    {
        $this->server->on($name, $callback);

        return $this;
    }

    public function setConfig($name, $value = null)
    {
        $this->context->set($name, $value);
    }
}