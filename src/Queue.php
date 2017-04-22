<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;


/**
 * Class Queue
 * @package FastD\Swoole
 */
class Queue extends Process
{
    /**
     * Queue constructor.
     * @param $name
     * @param $callback
     * @param bool $stdout
     * @param bool $pipe
     */
    public function __construct($name = null, $callback = null, $stdout = false, $pipe = true)
    {
        parent::__construct($name, $callback, $stdout, $pipe);

        $this->process->useQueue();
    }

    /**
     *
     */
    public function clean()
    {
        $this->process->freeQueue();
    }

    /**
     * @return mixed
     */
    public function state()
    {
        return $this->process->statQueue();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function push($data)
    {
        return $this->process->push($data);
    }

    /**
     * @param int $maxsize
     * @return mixed
     */
    public function pop($maxsize = 8192)
    {
        return $this->process->pop($maxsize);
    }
}