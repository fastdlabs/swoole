<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

use FastD\Swoole\Server\TCPServer;

include __DIR__ . '/../vendor/autoload.php';

use FastD\Http\Request;

class BaseServer extends \FastD\Swoole\HTTPServer
{
    /**
     * @param Request $request
     * @return \FastD\Http\Response
     */
    public function handleRequest(Request $request): \FastD\Http\Response
    {
        return new \FastD\Http\Response('hello world');
    }
}

BaseServer::createServer()->start();
