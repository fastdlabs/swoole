<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Tools;

use FastD\Swoole\Exceptions\AddressIllegalException;
use FastD\Swoole\Exceptions\CantSupportSchemeException;

trait Scheme
{
    public function parse($address)
    {
        if (false === ($info = parse_url($address))) {
            throw new AddressIllegalException($address);
        }

        switch (strtolower($info['scheme'])) {
            case 'tcp':
            case 'unix':
                $sock = SWOOLE_SOCK_TCP;
                break;
            case 'udp':
                $sock = SWOOLE_SOCK_UDP;
                break;
            case 'http':
            case 'ws':
                $sock = null;
                break;
            default:
                throw new CantSupportSchemeException($info['scheme']);
        }

        return array_merge($info, [
            'sock' => $sock
        ]);
    }
}