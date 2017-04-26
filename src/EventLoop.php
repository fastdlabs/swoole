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

class EventLoop
{
    protected $loop = [];

    public function set(Event $eventAbstract)
    {
        \swoole_event_add(
            $eventAbstract->getResource(),
            [$eventAbstract, 'onRead'],
            [$eventAbstract, 'doWrite'],
            $eventAbstract->getFlag()
        );
    }

    public function remove(Event $eventAbstract)
    {
        \swoole_event_del($eventAbstract->getResource());
    }

    public function exit()
    {
        \swoole_event_exit();
    }

    public function wait()
    {
        \swoole_event_wait();
    }

    public function write(Event $eventAbstract, $data)
    {
        return \swoole_event_write($eventAbstract->getResource(), $data);
    }
}