<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Coroutine;


/**
 * Class Redis
 * @package FastD\Swoole\Coroutine
 */
class Redis
{
    /**
     * Redis constructor.
     * @param $address
     */
    public function __construct($address)
    {
        $info = parse_url($address);

        $this->host = $info['host'];
        $this->port = $info['port'];

        $redis = new Swoole\Coroutine\Redis();
        $redis->connect($this->host, $this->port);
    }
}