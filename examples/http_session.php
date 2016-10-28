<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

use FastD\Swoole\Server\Http\HttpServer;

include __DIR__ . '/../vendor/autoload.php';

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
                return $this->html('ok');
            case '/session/get':
                return $this->json($request->session->toArray());
            default:
                return $this->html('hello swoole http server');
        }
    }
}

Http::run('http://0.0.0.0:9527', SWOOLE_PROCESS, [
    'debug' => true
]);