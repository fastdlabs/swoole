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
 * 统一 Swoole 响应, 支持 tcp, udp。
 *
 * Class Response
 *
 * @package FastD\Swoole
 */
class Response
{
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
     * @param \swoole_server|\swoole_http_response $server
     * @param $fd
     * @param $data
     */
    public function __construct($server, $fd, $data)
    {
        $this->server = $server;

        $this->fd = $fd;

        $this->data = $data;
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
     * @return void
     */
    public function send()
    {
        $this->server->send($this->getFd(), $this->getContent());

        $this->server->close($this->fd);
    }
}