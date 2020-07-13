<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Handlers;


use FastD\Http\ServerRequest;
use Swoole\Http\Request;
use Swoole\Http\Response;

interface HTTPHandlerInterface
{
    /**
     * @param Request $swooleRequet
     * @param Response $swooleResponse
     */
    public function onRequest(Request $swooleRequet, Response $swooleResponse): void;

    /**
     * @param ServerRequestInterface $serverRequest
     * @return \FastD\Http\Response
     */
    public function handleRequest(ServerRequest $serverRequest): \FastD\Http\Response;
}
