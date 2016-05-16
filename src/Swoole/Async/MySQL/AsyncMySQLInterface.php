<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/3
 * Time: 下午6:56
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Async\MySQL;

use FastD\Swoole\Async\AsyncInterface;

interface AsyncMySQLInterface extends AsyncInterface
{
    public function setMySQLi(\mysqli $mysqli);
}