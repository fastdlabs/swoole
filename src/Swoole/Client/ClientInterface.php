<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/12
 * Time: 下午4:38
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Client;

use FastD\Swoole\Handler\HandlerInterface;
use FastD\Swoole\SwooleInterface;

/**
 * Interface ClientInterface
 *
 * @package FastD\Swoole\Client
 */
interface ClientInterface extends SwooleInterface
{
    /**
     * @param HandlerInterface $handlerInterface
     * @return mixed
     */
    public function handle(HandlerInterface $handlerInterface);

    /**
     * @param $data
     * @return mixed
     */
    public function send($data);

    /**
     * @param      $host
     * @param      $port
     * @param null $flag
     * @return mixed
     */
    public function connect($host, $port, $flag = null);

    /**
     * @return mixed
     */
    public function receive();

    /**
     * @return mixed
     */
    public function close();
}