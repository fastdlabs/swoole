<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2017
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;


/**
 * Class UnixServer
 * @package FastD\Swoole
 */
class UnixServer extends ServerAbstract
{
    protected string $protocol = 'unix';
}
