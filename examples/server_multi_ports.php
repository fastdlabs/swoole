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
use FastD\Swoole\Request;

/**
 * Class DemoServer
 */
class DemoServer extends Server
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function doWork(Request $request)
    {
        return 'hello tcp server from ' . $request->getFd();
    }

    /**
     * @param Request $request
     * @return string
     */
    public function doPacket(Request $request)
    {

    }
}

DemoServer::run([
    'ports' => [
        [
            'host' => '0.0.0.0',
            'port' => '9999',
            'sock' => SWOOLE_SOCK_TCP,
        ],
        [
            'host' => '0.0.0.0',
            'port' => '9998',
            'sock' => SWOOLE_SOCK_TCP,
        ],
    ]
]);

/**
 * 以上写法和以下写法效果一致
 *
 * $test = new DemoServer([
 *  'ports' => [
 *      [
 *          'host' => '0.0.0.0',
 *          'port' => '9999',
 *          'sock' => SWOOLE_SOCK_TCP,
 *      ],
 *      [
 *          'host' => '0.0.0.0',
 *          'port' => '9998',
 *          'sock' => SWOOLE_SOCK_TCP,
 *      ],
 *  ]
 * ]);
 * $test->start();
 */
