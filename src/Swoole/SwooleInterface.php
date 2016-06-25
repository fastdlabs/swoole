<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/1/29
 * Time: 下午11:55
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole;

/**
 * Interface SwooleInterface
 *
 * @package FastD\Swoole
 */
interface SwooleInterface
{
    const LOGO = <<<LOGO
    ____    __
   / __/___/ /
  / /_/ __  /
 / __/ /_/ /
/_/  \__,_/

LOGO;

    /**
     * @param $name
     * @param $callback
     * @return $this
     */
    public function on($name, $callback);

    /**
     * @return array
     */
    public function configure();
}