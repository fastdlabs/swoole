<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

include __DIR__ . '/../../vendor/autoload.php';

class DemoTimer extends \FastD\Swoole\Timer
{
    protected $count = 0;

    /**
     * @param $id
     * @param array $params
     * @return mixed
     */
    public function doTick($id, array $params = [])
    {
        echo ++$this->count;
        if (3 === $this->count) {
            $this->clear();
        }
    }
}

return new DemoTimer();