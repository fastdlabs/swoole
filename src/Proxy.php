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
    /**
     * @var string
     */
    protected $url;

    /**
     * @var bool
     */
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
            $this->forward('http://'.$domain, $async);
        });
    }

    /**
     * @param $url
     * @param string $data
     * @param array $headers
     * @return mixed
     */
    public function forward($url, $data = '', array $headers = [])
    {
        $client = new Client($url);
        $client->setHeaders($headers);
        $content = $client->send($data);
        list($headers, $content) = explode("\r\n\r\n", $content);
        return call_user_func_array([$this, 'handle'], [$headers, $content]);
    }

    /**
     * @param $headers
     * @param $response
     * @return mixed
     */
    abstract public function handle($headers, $response);
}