<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Http;

use FastD\Http\Response;
use FastD\Http\SwooleRequest;
use FastD\Swoole\Server\ServerInterface;

/**
 * Interface HttpServerInterface
 *
 * @package FastD\Swoole\Server
 */
interface HttpServerInterface extends ServerInterface
{
    /**
     * @param SwooleRequest $request
     * @return Response
     */
    public function doRequest(SwooleRequest $request);
}