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
 * Class Request
 *
 * @package FastD\Swoole
 */
class Request
{
    /**
     * @var \swoole_server
     */
    protected $server;

    /**
     * @var int
     */
    protected $fd;

    /**
     * @var string
     */
    protected $data;

    /**
     * @var int
     */
    protected $fromId;

    /**
     * @var null
     */
    protected $clientInfo;

    /**
     * Request constructor.
     *
     * @param \swoole_server $server
     * @param $fd
     * @param $data
     * @param null $from_id
     * @param null $clientInfo
     */
    public function __construct(\swoole_server $server, $fd, $data, $from_id = null, $clientInfo = null)
    {
        $this->server = $server;

        $this->fd = $fd;

        $this->data = $data;

        $this->fromId = $from_id;

        $this->clientInfo = $clientInfo;
    }

    /**
     * @return \swoole_server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return int
     */
    public function getFd()
    {
        return $this->fd;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int|null
     */
    public function getFromId()
    {
        return $this->fromId;
    }

    /**
     * @return null
     */
    public function getClientInfo()
    {
        return $this->clientInfo;
    }
}