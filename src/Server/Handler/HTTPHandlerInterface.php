<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Server\Handler;


use Swoole\Http\Request;
use Swoole\Http\Response;

interface HTTPHandlerInterface
{
    /**
     * @param Request $request
     * @param Response $response
     */
    public function onRequest(Request $request, Response $response): void;
}
