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

/**
 * Class Context
 *
 * @package FastD\Swoole
 */
class Context
{
    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $port;

    /**
     * @var array
     */
    protected $config = [
        'sock_file' => '/tmp/swoole.sock'
    ];

    /**
     * @param       $protocol
     * @param array $config
     */
    public function __construct($protocol, array $config = ['worker_num' => 1, 'daemonize' => false])
    {
        $protocol = parse_url($protocol);
        $this->scheme = $protocol['scheme'];
        $this->host = $protocol['host'];
        $this->port = $protocol['port'];
        $this->config = array_merge($this->config, $config);
        unset($protocol, $config);
    }

    /**
     * @return string
     */
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
        $this->scheme = $schema;
        return $this;
    }

    /**
     * @return string
     */
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

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->config[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @return null
     */
    public function get($name)
    {
        return isset($this->config[$name]) ? $this->config[$name] : null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->config[$name]);
    }

    /**
     * @param $name
     * @param $default
     * @return mixed
     */
    public function hasGet($name, $default)
    {
        if (!$this->has($name)) {
            return $default;
        }

        return $this->get($name);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->config;
    }
}