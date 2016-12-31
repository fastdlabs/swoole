<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;


use swoole_server;

abstract class Task extends Tcp
{
    abstract public function doTask();

    public function onTask()
    {

    }

    abstract public function doFinish();

    public function onFinish()
    {

    }
}