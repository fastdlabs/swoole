<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Client\Async;

use FastD\Swoole\Client;
use swoole_client;

/**
 * Class AsyncClient
 *
 * @package FastD\Swoole\Async
 */
class TCP extends Client
{
    /**
     * @param $data
     * @param bool $async
     * @param null $keep
     * @return mixed
     */
    public function send($data, $async = true, $keep = null)
    {
        return parent::send($data, $async, $keep);
    }
}