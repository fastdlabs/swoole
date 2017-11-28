<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;


use FastD\Http\Cookie;
use swoole_client;
use swoole_http_client;

/**
 * Class Client
 *
 * @package FastD\Swoole
 */
class Client
{
    const HTTP_VERSION = '1.1';
    const USER_AGENT = 'PHP swoole/2.1 (+https://github.com/fastdlabs/swoole)';

    /**
     * @var swoole_client
     */
    protected $client;

    /**
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var string
     */
    protected $path = '/';

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var Cookie[]
     */
    protected $cookies = [];

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
     * @var int
     */
    protected $socketType = SWOOLE_SOCK_TCP;

    /**
     * @var int
     */
    protected $timeout = 1;

    /**
     * Client constructor.
     * @param $uri
     * @param bool $async
     * @param bool $keep
     */
    public function __construct($uri = null, $async = false, $keep = false)
    {
        if (null !== $uri) {
            $this->createRequest($uri, $async, $keep);
        }
    }

    /**
     * @param $url
     * @param bool $async
     * @param bool $keep
     * @return $this
     */
    public function createRequest($url, $async = false, $keep = false)
    {
        $info = parse_url($url);
        $this->scheme = isset($info['scheme']) ? $info['scheme'] : 'http';
        $this->host = $info['host'];
        $this->port = isset($info['port']) ? $info['port'] : 80;

        switch ($this->scheme) {
            case 'tcp':
            case 'http':
                $socketType = SWOOLE_SOCK_TCP;
                break;
            case 'udp':
                $socketType = SWOOLE_SOCK_UDP;
                break;
            default:
                throw new \LogicException("Don't support schema ".$info['scheme']);
        }

        $this->path = isset($info['path']) ? $info['path'] : '/';

        $sync = false === $async ? SWOOLE_SOCK_SYNC : SWOOLE_SOCK_ASYNC;
        $this->socketType = true === $keep ? ($socketType | SWOOLE_KEEP) : $socketType;

        // async
        if ($async && false !== strpos($this->scheme, 'http')) {
            $this->client = new swoole_http_client($this->host, $this->port);
        } else {
            $this->client = new swoole_client($this->socketType, $sync);
        }

        return $this;
    }

    /**
     * @param string|array $data
     * @return string
     */
    protected function wrapBody($data = '')
    {
        if (is_array($data)) {
            $data = http_build_query($data);
        }

        if (false !== strpos($this->scheme, 'http')) {
            $cookies = '';
            if ( ! in_array($this->method, ['GET', 'HEAD', 'OPTIONS'])) {
                $this->setHeader('Content-Length', strlen($data));
                $this->setHeader('Content-Type', 'application/x-www-form-urlencoded');
            } else {
                $this->path .= '?'.$data;
            }
            foreach ($this->cookies as $cookie) {
                $cookies .= $cookie->asString();
            }
            $ua = static::USER_AGENT;
            $version = static::HTTP_VERSION;
            $header = "{$this->method} {$this->path} HTTP/{$version}\r\n";
            foreach ($this->headers as $key => $value) {
                $header .= "$key: ".(is_array($value) ? implode(',', $value) : $value)."\r\n";
            }
            if ( ! empty($cookies)) {
                $header .= "Cookie: {$cookies}\r\n";
            }
            $header .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8\r\n";
            $header .= "User-Agent: {$ua}\r\n";
            $header .= "\r\n";

            $data = $header.$data;
        }

        return $data;
    }

    /**
     * @param $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param $name
     * @param null $value
     * @param null $expire
     * @param null $path
     * @param null $domain
     * @param null $secure
     * @param null $httpOnly
     * @return $this
     */
    public function setCookie(
        $name,
        $value = null,
        $expire = null,
        $path = null,
        $domain = null,
        $secure = null,
        $httpOnly = null
    ) {
        $this->cookies[] = new Cookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);

        return $this;
    }

    /**
     * @param array $cookies
     * @return $this
     */
    public function setCookies(array $cookies)
    {
        $this->cookies = array_merge($this->cookies, $cookies);

        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * @param $name
     * @param $handler
     * @return mixed
     */
    public function on($name, $handler)
    {
        $this->client->on($name, $handler);

        return $this;
    }

    /**
     * @param $configure
     * @return $this
     */
    public function configure($configure)
    {
        $this->client->set($configure);

        return $this;
    }

    /**
     * @param swoole_client $client
     * @return mixed
     */
    public function connect(swoole_client $client)
    {
    }

    /**
     * @param swoole_client $client
     * @param string $data
     * @return mixed
     */
    public function receive(swoole_client $client, $data)
    {
    }

    /**
     * @param swoole_client $client
     * @return mixed
     */
    public function error(swoole_client $client)
    {
    }

    /**
     * @param swoole_client $client
     * @return mixed
     */
    public function close(swoole_client $client)
    {
    }

    /**
     * @param string|array $data
     * @return mixed
     */
    public function send($data = '')
    {
        if ( ! $this->client->connect($this->host, $this->port, $this->timeout)) {
            throw new \RuntimeException(socket_strerror($this->client->errCode));
        }

        $this->client->send($this->wrapBody($data));
        $receive = $this->client->recv();
        $this->client->close();

        return $receive;
    }

    /**
     * start async client
     */
    public function start()
    {
        $this->client->on("connect", function ($client) {
            call_user_func_array([$this, 'connect'], [$client]);
        });
        $this->client->on("receive", function ($client, $data) {
            call_user_func_array([$this, 'receive'], [$client, $data]);
        });
        $this->client->on("error", function ($client) {
            call_user_func_array([$this, 'error'], [$client]);
        });
        $this->client->on("close", function ($client) {
            call_user_func_array([$this, 'close'], [$client]);
        });
        $this->client->connect($this->host, $this->port, $this->timeout);
    }
}