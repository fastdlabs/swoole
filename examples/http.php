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
use FastD\Http\Request;

class Http extends HttpServer
{
    /**
     * @param Request $request
     * @return \FastD\Http\Response
     */
    public function doRequest(\FastD\Http\Request $request)
    {
        return $this->json(['name' => 'jan']);
    }
}

Http::run([
    'host' => '0.0.0.0',
]);
