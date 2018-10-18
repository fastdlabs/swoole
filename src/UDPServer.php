<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;

use FastD\Swoole\Handlers\UDPServerHandlerInterface;


/**
 * Class UDPServer
 * @package FastD\Swoole
 */
abstract class UDPServer extends ServerAbstract implements UDPServerHandlerInterface
{

}