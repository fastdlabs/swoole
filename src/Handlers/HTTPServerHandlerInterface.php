<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Handlers;


use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * Interface HTTPServerHandlerInterface
 * @package FastD\Swoole
 */
interface HTTPServerHandlerInterface extends HandlerInterface
{
    /**
     * Handle http request.
     *
     * @param Request $request
     * @param Response $response
     */
    public function onRequest(Request $request, Response $response): void;
}