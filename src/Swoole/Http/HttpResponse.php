<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Http;

use FastD\Swoole\Response;

/**
 * Class HttpResponse
 * @package FastD\Swoole\Http
 */
class HttpResponse extends Response
{
    /**
     * @var bool
     */
    protected $keepAlive = false;

    /**
     * HttpResponse constructor.
     * @param \swoole_http_response $swooleResponse
     * @param $data
     */
    public function __construct(\swoole_http_response $swooleResponse, $data)
    {
        parent::__construct($swooleResponse, null, $data);
    }

    /**
     * @param array $header
     */
    public function setHeaders(array $header)
    {
        foreach ($header as $key => $value) {
            $this->server->header($key, $value);
        }

        if ($this->isKeepAlive()) {
            $this->server->header('Connection', 'keep-alive');
        }
    }

    /**
     * @param array $cookies
     */
    public function setCookies(array $cookies)
    {
        foreach ($cookies as $name => $value) {
            $this->server->cookie($name, $value);
        }
    }

    /**
     * @param $status
     */
    public function setStatus($status)
    {
        $this->server->status($status);
    }

    /**
     * @return bool
     */
    public function isKeepAlive()
    {
        return $this->keepAlive;
    }

    /**
     * @return void
     */
    public function send()
    {
        $this->server->end($this->getContent());
    }
}
