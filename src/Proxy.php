<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2017
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace FastD\Swoole;


use FastD\Swoole\AsyncIO\DNS;

/**
 * Class Proxy
 * @package FastD\Swoole
 */
abstract class Proxy
{
    protected $url;

    protected $async = false;

    /**
     * Proxy constructor.
     * @param $url
     * @param bool $async
     */
    public function __construct($url, $async = false)
    {
        $this->url = $url;

        $this->async = $async;
    }

    public function run()
    {
        $dns = new DNS($this->url);
        $async = $this->async;
        $dns->lookup(function ($domain, $ip) use ($async) {
            $client = new Client('http://'.$domain, $async);
            $content = $client->send();
            list($headers, $content) = explode("\r\n\r\n", $content);
            call_user_func_array([$this, 'handle'], [$headers, $content]);
        });
    }

    /**
     * @param $headers
     * @param $response
     * @return mixed
     */
    abstract public function handle($headers, $response);
}