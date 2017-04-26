<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace FastD\Swoole\AsyncIO;


/**
 * Class DNS
 * @package FastD\Swoole\AsyncIO
 */
class DNS
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * DNS constructor.
     * @param $host
     * @param array $options
     */
    public function __construct($host, array $options = [])
    {
        $this->host = $host;

        $this->options = $options;
    }

    /**
     * @param null $callback
     */
    public function lookup($callback = null)
    {
        swoole_async_set($this->options);
        swoole_async_dns_lookup($this->host, null === $callback ? [$this, 'doLookup'] : $callback);
    }

    public function doLookup($host, $ip) {}
}