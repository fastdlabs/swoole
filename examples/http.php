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

use FastD\Swoole\Http\HttpServer;

class Http extends HttpServer
{
    /**
     * @param \FastD\Swoole\Request $request
     * @return \FastD\Swoole\Response
     */
    public function doRequest(\FastD\Swoole\Request $request)
    {
        return $this->html('hello world');
    }
}

Http::run([
    'log_file' => './fds.log',
    'host' => '0.0.0.0',
]);
