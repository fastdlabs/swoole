<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Exceptions;

class ServerCannotConnectionException extends SwooleException
{
    /**
     * AddressIllegalException constructor.
     *
     * @param string $host
     * @param $port
     */
    public function __construct($host, $port)
    {
        parent::__construct(sprintf('Server %s:%s connection fail.', $host, $port));
    }
}