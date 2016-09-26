<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/16
 * Time: 下午10:22
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Tests;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    protected $workspace_dir;

    public function setUp()
    {
        $this->workspace_dir = isset($_SERVER['PWD']) ? $_SERVER['PWD'] : realpath('.');
    }

    public function testDefaultParseServerIniFileConfig()
    {

    }

    public function testConstructionArguments()
    {

    }

    public function testConfiguration()
    {

    }
}
