<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Pool;


class Process
{
    protected int $worker_num;

    public function __construct(int $worker_num)
    {
        $this->worker_num = $worker_num;
    }

    public function start()
    {

    }
}
