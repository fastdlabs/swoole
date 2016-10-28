<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Exceptions;

/**
 * Class AddressIllegalException
 *
 * @package FastD\Swoole\Exceptions
 */
class AddressIllegalException extends SwooleException
{
    /**
     * AddressIllegalException constructor.
     *
     * @param string $address
     */
    public function __construct($address)
    {
        parent::__construct(sprintf('The address "%s" is illegal.', $address));
    }
}