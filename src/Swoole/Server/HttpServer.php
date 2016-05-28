<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/3/7
 * Time: 上午11:50
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Server;

/**
 * Class HttpServer
 *
 * @package FastD\Swoole\Server
 */
class HttpServer extends Server
{
    /**
     * @var string
     */
    const SERVER_NAME = 'fd-http';

    /**
     * Enable Http2 Support.
     *
     * @return $this
     */
    public function enableHttp2()
    {
        $this->config['open_http2_protocol'] = true;

        return $this;
    }

    /**
     * Enable SSL Support.
     *
     * @param $crt
     * @param $key
     * @return $this
     */
    public function enableSSL($crt, $key)
    {
        $this->config['ssl_cert_file'] = $crt;
        $this->config['ssl_key_file'] = $key;

        return $this;
    }

    /**
     * @return \swoole_server
     */
    public function initSwooleServer()
    {
        return new \swoole_http_server($this->getHost(), $this->getPort(), $this->getMode(), $this->getSock());
    }
}