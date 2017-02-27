<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Client\Sync;

use FastD\Swoole\Client;
use FastD\Swoole\Exceptions\ServerCannotConnectionException;

/**
 * Class SyncClient
 *
 * @package FastD\Swoole\Client\Sync
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
}