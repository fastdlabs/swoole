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

/**
 * Interface ClientInterface
 *
 * @package FastD\Swoole\Client
 */
interface ClientInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function send($data);

    /**
     * @param      $host
     * @param      $port
     * @param int  $timeout
     * @return mixed
     */
    public function connect($host, $port, $timeout = 5);

    /**
     * @return mixed
     */
    public function receive();

    /**
     * @return mixed
     */
    public function close();
}