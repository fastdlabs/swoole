<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Server;


use FastD\Http\Response;
use FastD\Http\ServerRequest;
use FastD\Swoole\Handlers\HTTPHandlerInterface;
use Swoole\Http\Server;

/**
 * Class HTTPServer
 * @package FastD\Swoole\Server
 */
class HTTPServer extends ServerAbstract
{
    /**
     * @var string
     */
    protected string $protocol = 'http';

    /**
     * @var string
     */
    protected string $handler;

    /**
     * @return \Swoole\Server
     */
    public function initSwoole(): \Swoole\Server
    {
        return new Server($this->host, $this->port);
    }

    /**
     * 开启 http2 需要 ssl配置
     * @param string $key
     * @param string $cert
     * @return HTTPServer
     */
    public function enableHTTP2(string $key, string $cert): HTTPServer
    {
        $this->config['open_http2_protocol'] = true;
        $this->config['ssl_cert_file'] = $cert;
        $this->config['ssl_key_file'] = $key;

        return $this;
    }
}
