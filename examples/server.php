<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/1/18
 * Time: 下午9:47
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

include __DIR__ . '/../vendor/autoload.php';

use FastD\Swoole\Server;

class DemoServer extends Server
{
    /**
     * @param \FastD\Swoole\Request $request
     * @return \FastD\Swoole\Response
     */
    public function doWork(\FastD\Swoole\Request $request)
    {
        return $this->response($request->getServer(), $request->getFd(), $request->getData());
    }

    /**
     * @param \FastD\Swoole\Request $request
     * @return \FastD\Swoole\Response
     */
    public function doPacket(\FastD\Swoole\Request $request)
    {
        // TODO: Implement doPacket() method.
    }
}

DemoServer::run([]);

/**
 * 以上写法和以下写法效果一致
 *
 * $test = new DemoServer();
 * $test->start();
 */
