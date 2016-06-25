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
 * Interface HttpServerInterface
 *
 * @package FastD\Swoole\Server
 */
interface HttpServerInterface extends ServerInterface
{
    /**
     * @param \swoole_http_request $request
     * @param \swoole_http_response $response
     * @return mixed
     */
    public function doRequest(\swoole_http_request $request, \swoole_http_response $response);
}