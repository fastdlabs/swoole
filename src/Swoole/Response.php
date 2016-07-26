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
    public function __construct(\swoole_server $server, $fd, $data, $keepAlive = false)
    {
        $this->server = $server;

        $this->fd = $fd;

        $this->data = $data;

        $this->keepAlive = $keepAlive;
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
        $this->server->send($this->getFd(), $this->getContent());

        if (!$this->isKeepAlive()) {
            $this->server->close($this->fd);
        }
    }
}