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
     * @param HttpRequest $request
     * @return string
     */
    public function doRequest(HttpRequest $request)
    {
        return $this->html($request->getPathInfo());
    }
}

Http::run([
    'log_file' => './fds.log',
    'host' => '0.0.0.0',
]);