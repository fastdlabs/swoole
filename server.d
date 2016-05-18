<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/19
 * Time: 上午12:51
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

include __DIR__ . '/bootstrap.php';

$prefix = '@prefix@';
$pid = '@pid@';

if (isset($_SERVER['argv'][1])) {
    $action = $_SERVER['argv'][1];
}


 