<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;

use FastD\Swoole\Handlers\UDPHandlerInterface;


/**
 * Class UDPServer
 * @package FastD\Swoole
 */
abstract class UDPServer extends ServerAbstract implements UDPHandlerInterface
{
    protected $protocol = 'udp';
}
