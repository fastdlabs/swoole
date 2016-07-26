<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;

/**
 * 统一 Swoole 响应, 支持 tcp, udp, http。
 *
 * Class Response
 *
 * @package FastD\Swoole
 */
class Response
{
    /**
     * @var bool
     */
    protected $keepAlive = false;

    /**
     * @var int
     */
    protected $fd;

    /**
     * @var string
     */
    protected $data;

    /**
     * @var \swoole_server
     */
    protected $server;

    /**
     * Response constructor.
     *
     * @param \swoole_server $server
     * @param $fd
     * @param $data
     * @param bool $keepAlive
     */
    public function __construct($server, $fd, $data, $keepAlive = false)
    {
        $this->server = $server;

        $this->fd = $fd;

        $this->data = $data;

        $this->keepAlive = $keepAlive;
    }

    public function setHeaders(array $header)
    {
        foreach ($header as $key => $value) {
            $this->server->header($key, $value);
        }
    }

    public function setCookies(array $cookies)
    {
        foreach ($cookies as $name => $value) {
            $this->server->cookie($name, $value);
        }
    }

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
     * @return \swoole_server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getFd()
    {
        return $this->fd;
    }

    /**
     *
     */
    public function send()
    {
        if ($this->server instanceof \swoole_http_response) {
            $this->server->end($this->getContent());
        } else {
            $this->server->send($this->getFd(), $this->getContent());

            if (!$this->isKeepAlive()) {
                $this->server->close($this->fd);
            }
        }
    }
}