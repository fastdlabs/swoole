<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Exceptions;

use FastD\Http\Exceptions\HttpException;
use FastD\Http\Response;

class CannotResponseException extends HttpException
{
    public function __construct()
    {
        parent::__construct('Cannot response. You must be return Response Object on doRequest method.');
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}