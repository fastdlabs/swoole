<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2017
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace FastD\Swoole;


/**
 * Class UnixServer
 * @package FastD\Swoole
 */
abstract class UnixServer extends ServerAbstract
{
    protected $protocol = 'unix';

    protected $port = 0;

    public function __construct(string $address = null, array $config = [])
    {
        $this->host = $address;

        parent::__construct(null);
    }
}