<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2017
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

include __DIR__ . '/../../vendor/autoload.php';

class Proxy extends \FastD\Swoole\Proxy
{
    /**
     * @param $headers
     * @param $response
     * @return mixed
     */
    public function handle($headers, $response)
    {
        echo $headers;
        echo $response;
    }
}

$proxy = new Proxy('localhost:9527');

$proxy->run();