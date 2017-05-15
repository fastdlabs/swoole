<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace FastD\Swoole;


/**
 * Class Timer
 * @package FastD\Swoole
 */
abstract class Timer
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $ms;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var Server
     */
    protected $server;

    /**
     * Timer constructor.
     * @param int $ms
     * @param array $params
     */
    public function __construct($ms = 1000, array $params = [])
    {
        $this->ms = $ms;

        $this->params = $params;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param Server $server
     * @return $this
     */
    public function withServer(Server $server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * @return int
     */
    public function tick()
    {
        $this->id = timer_tick($this->ms, [$this, 'doTick'], $this->params);

        return $this->id;
    }

    /**
     * @return int
     */
    public function after()
    {
        $this->id = timer_after($this->ms, [$this, 'doTick'], $this->params);

        return $this->id;
    }

    /**
     * @return mixed
     */
    public function clear()
    {
        return timer_clear($this->id);
    }

    /**
     * @param $id
     * @param array $params
     * @return mixed
     */
    abstract public function doTick($id, array $params = []);
}