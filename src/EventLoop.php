<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace FastD\Swoole;


use FastD\Swoole\AsyncIO\Event;

/**
 * Class EventLoop
 * @package FastD\Swoole
 */
class EventLoop
{
    /**
     * @var Event[]
     */
    protected $events = [];

    /**
     * @param Event $eventAbstract
     */
    public function set(Event $eventAbstract)
    {
        $key = spl_object_hash($eventAbstract);
        if (isset($this->events[$key])) {
            \swoole_event_set(
                $eventAbstract->getResource(),
                [$eventAbstract, 'onRead'],
                [$eventAbstract, 'doWrite'],
                $eventAbstract->getFlag()
            );
        } else {
            \swoole_event_add(
                $eventAbstract->getResource(),
                [$eventAbstract, 'onRead'],
                [$eventAbstract, 'doWrite'],
                $eventAbstract->getFlag()
            );
            $eventAbstract->setEventLoop($this);
        }
        $this->events[$key] = $eventAbstract;
    }

    /**
     * @param Event $eventAbstract
     */
    public function remove(Event $eventAbstract)
    {
        \swoole_event_del($eventAbstract->getResource());

        unset($this->events[spl_object_hash($eventAbstract)]);
        if (empty($this->events)) {
            $this->exit();
        }
    }

    /**
     * Exit event loop
     */
    public function exit()
    {
        \swoole_event_exit();
    }

    /**
     * @param Event $eventAbstract
     * @param $data
     * @return mixed
     */
    public function write(Event $eventAbstract, $data)
    {
        return \swoole_event_write($eventAbstract->getResource(), $data);
    }
}