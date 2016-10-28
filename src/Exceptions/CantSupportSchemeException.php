<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Exceptions;

class CantSupportSchemeException extends SwooleException
{
    public function __construct($scheme)
    {
        parent::__construct(sprintf("Can't support this scheme: %s", $scheme));
    }
}