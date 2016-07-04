<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;

/**
 * Interface ServerDiscovery
 *
 * @package FastD\Swoole\Server
 */
interface ServerDiscoveryInterface
{
    /**
     * @param array $discoveries
     * @return mixed
     */
    public function discovery(array $discoveries);
}