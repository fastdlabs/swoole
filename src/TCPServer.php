<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;

use FastD\Swoole\Handlers\TCPServerHandlerInterface;

/**
 * Class TCPServer
 * @package FastD\Swoole
 */
abstract class TCPServer extends ServerAbstract implements TCPServerHandlerInterface
{
    protected $protocol = 'tcp';
}