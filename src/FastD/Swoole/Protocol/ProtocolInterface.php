<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/3/7
 * Time: 下午4:51
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Protocol;

/**
 * 协议标准
 *
 * Interface ProtocolInterface
 *
 * @package FastD\Swoole\Protocol
 */
interface ProtocolInterface
{
    const EOF = 'FFF';
    const SALT = "=&$*#@(*&%(@";
    const MAX_LENGTH = 1024;
    const RECEIVE_TIMEOUT = 3;
    const OPEN_AUTHORIZATION = false;

    /**
     * 编码数据包
     *
     * @return string
     */
    public function encode();

    /**
     * 解包
     *
     * @return null|string|array
     */
    public function decode();
}