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
class UDP extends Client
{
    /**
     * UDP constructor.
     * @param $address
     * @param $socketType
     */
    public function __construct($address, $socketType = SWOOLE_SOCK_UDP)
    {
        parent::__construct($address, $socketType);
    }

    /**
     * @param $data
     * @param bool $async
     * @param bool $keep
     * @return mixed
     */
    public function send($data, $async = true, $keep = null)
    {
        return parent::send($data, $async, $keep);
    }
}