<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Async;

use FastD\Swoole\Client;

class AsyncHttp extends Client
{
    public function __construct($address, $mode)
    {
        parent::__construct($address, $mode);
    }
}