<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

use FastD\Swoole\EventLoop;

include __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/BaseEvent.php';

$loop = new EventLoop();

$loop->set(BaseEvent::create(STDIN));

