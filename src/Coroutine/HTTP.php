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
 * Class Http
 * @package FastD\Swoole\Coroutine
 */
class HTTP
{
    /**
     * Http constructor.
     * @param $address
     * @param $mode
     */
    public function __construct($address, $mode = SWOOLE_SOCK_TCP)
    {
        $info = $this->parse($address);

        $this->host = $info['host'];
        $this->port = $info['port'];

        $this->client = new \Swoole\Coroutine\Http\Client($this->host, $this->port);
    }
}