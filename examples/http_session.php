<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../vendor/autoload.php';

use FastD\Swoole\Http\HttpServer;
use FastD\Swoole\Http\HttpRequest;

class Http extends HttpServer
{
    /**
     * @param \FastD\Http\SwooleServerRequest $request
     * @return \FastD\Http\Response
     */
    public function doRequest(\FastD\Http\SwooleServerRequest $request)
    {
        switch ($request->server->getPathInfo()) {
            case '/session/set':
                $request->session->set('user', [
                    'name' => 'jan',
                    'age' => 19
                ]);
                break;
            case '/session/get':
                return $this->json($request->session->toArray());
                break;
            default:
                return $this->html('hello swoole http server');
        }
    }
}

Http::run([
    'log_file' => './fds.log',
    'host' => '0.0.0.0',
]);