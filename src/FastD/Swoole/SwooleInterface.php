<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/10
 * Time: 上午11:55
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */
namespace FastD\Swoole;

/**
 * Interface SwooleInterface
 *
 * @package FastD\Swoole
 */
interface SwooleInterface
{
    /**
     * @return mixed
     */
    public function status();

    /**
     * @return mixed
     */
    public function start();

    /**
     * @return mixed
     */
    public function stop();

    /**
     * @return mixed
     */
    public function reload();

    /**
     * @param      $name
     * @param null $callback
     * @return $this
     */
    public function on($name, $callback = null);

    /**
     * @param      $name
     * @param null $value
     * @return $this
     */
    public function setConfig($name, $value = null);

    /**
     * @param SwooleHandlerInterface $swooleHandlerInterface
     * @return $this
     */
    public function handle(SwooleHandlerInterface $swooleHandlerInterface);
}