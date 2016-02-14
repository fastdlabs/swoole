<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/2/14
 * Time: 下午6:45
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

include __DIR__ . '/../../vendor/autoload.php';

use FastD\Swoole\Manager\ServerManager;

$manager = new ServerManager();

$manager->watch([__DIR__ . '/tmp'], function () {
    echo 'callback';
});
