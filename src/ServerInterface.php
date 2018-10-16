<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Server;


/**
 * Interface ServerInterface
 * @package FastD\Swoole\Server
 */
interface ServerInterface
{
    /**
     * @param string $name
     * @return ServerInterface
     */
    public function rename(string $name): ServerInterface;

    /**
     * @param array $config
     * @return ServerInterface
     */
    public function configure(array $config): ServerInterface;

    /**
     * @return ServerInterface
     */
    public function daemon(): ServerInterface;

    /**
     * @return array
     */
    public function status(): array ;

    /**
     * @return int
     */
    public function start(): int;

    /**
     * @return int
     */
    public function restart(): int;

    /**
     * @return int
     */
    public function reload(): int;

    /**
     * @return int
     */
    public function stop(): int;
}