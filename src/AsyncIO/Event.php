<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace FastD\Swoole\AsyncIO;


use FastD\Swoole\EventLoop;

/**
 * Class EventAbstract
 * @package FastD\Swoole\AsyncIO
 */
class Event
{
    /**
     * @var EventLoop
     */
    protected $loop;

    /**
     * @var resource
     */
    protected $resource;

    /**
     * @var int
     */
    protected $flag;

    /**
     * EventAbstract constructor.
     * @param $resource
     * @param null $flag
     */
    public function __construct($resource, $flag = null)
    {
        $this->resource = $resource;

        $this->flag = $flag;
    }

    /**
     * @param EventLoop $eventLoop
     * @return $this
     */
    public function setEventLoop(EventLoop $eventLoop)
    {
        $this->loop = $eventLoop;

        return $this;
    }

    /**
     * @return EventLoop
     */
    public function getEventLoop()
    {
        return $this->loop;
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return int|null
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * @param $resource
     */
    public function onRead($resource)
    {
        $this->doRead(fread($resource, 8192));
    }

    /**
     * @param $data
     */
    public function doRead($data) {}

    /**
     * @param $resource
     */
    public function doWrite($resource) {}

    /**
     * Exit event
     */
    public function exit()
    {
        $this->loop->remove($this);
    }
}