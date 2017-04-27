<?php
use FastD\Swoole\AsyncIO\Event;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */
class BaseEvent extends Event
{
    public function doRead($data)
    {
        echo $data;

        if ('exit' == trim($data)) {
            $this->exit();
        }

        return true;
    }
}


