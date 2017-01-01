<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Exceptions;

use FastD\Http\Exception\HttpException;
use FastD\Http\Response;

/**
 * Class CannotResponseException
 * @package FastD\Swoole\Exceptions
 */
class CannotResponseException extends HttpException
{
    /**
     * CannotResponseException constructor.
     */
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