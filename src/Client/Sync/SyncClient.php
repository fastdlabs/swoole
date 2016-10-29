<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Client\Sync;

use FastD\Swoole\Client;
use FastD\Swoole\Exceptions\ServerCannotConnectionException;

class SyncClient extends Client
{
    protected $callbacks = [];

    /**
     * @param $callback
     * @param int $timeout
     * @return $this
     */
    public function connect($callback, $timeout = 5)
    {
        $this->callbacks['connect'] = $callback;

        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @param $callback
     * @return mixed
     */
    public function error($callback)
    {
        return true;
    }

    /**
     * @param $callback
     * @return $this
     */
    public function receive($callback)
    {
        $this->callbacks['receive'] = $callback;

        return $this;
    }

    /**
     * @return mixed
     */
    public function resolve()
    {
        if (!$this->client->connect($this->host, $this->port, $this->timeout)) {
            throw new ServerCannotConnectionException($this->host, $this->port);
        }

        $this->callbacks['connect']($this->client);

        $this->callbacks['receive']($this->client, $this->client->recv());
    }

    public function close($callback = null)
    {
        $this->client->close();
    }
}