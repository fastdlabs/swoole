<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/12
 * Time: 下午4:06
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Client;

use FastD\Swoole\SwooleInterface;

/**
 * Class Client
 *
 * @package FastD\Swoole\Client
 */
class Client extends \swoole_client implements ClientInterface, SwooleInterface
{
    /**
     * @return mixed
     */
    public function receive()
    {
        return $this->recv();
    }

    /**
     * @param $configure
     * @return $this
     */
    public function configure($configure)
    {
        $this->set($configure);

        return $this;
    }
}