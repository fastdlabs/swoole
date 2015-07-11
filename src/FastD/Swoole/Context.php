<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/10
 * Time: 上午11:22
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */
namespace FastD\Swoole;

class Context
{
    protected $scheme;
    protected $host;
    protected $port;
    protected $config;

    public function __construct($protocol, array $config = ['worker_num' => 1, 'daemonize' => false])
    {
        $protocol = parse_url($protocol);
        $this->schema = $protocol['scheme'];
        $this->host = $protocol['host'];
        $this->port = $protocol['port'];
        $this->config = $config;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @param mixed $schema
     * @return $this
     */
    public function setScheme($schema)
    {
        $this->schema = $schema;
        return $this;
    }

    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param mixed $host
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @param mixed $port
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function set($name, $value)
    {
        $this->config[$name] = $value;

        return $this;
    }

    public function get($name)
    {
        return isset($this->config[$name]) ? $this->config[$name] : null;
    }

    public function has($name)
    {
        return isset($this->config[$name]);
    }

    public function all()
    {
        return $this->config;
    }
}