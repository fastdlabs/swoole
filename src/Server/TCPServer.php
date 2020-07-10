<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;

use FastD\Swoole\Handlers\TCPHandlerInterface;

/**
 * Class TCPServer
 * @package FastD\Swoole
 */
abstract class TCPServer extends ServerAbstract implements TCPHandlerInterface
{
}
