<?php

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
class HelpersTest extends PHPUnit_Framework_TestCase
{
    public function testParseUri()
    {
        $info = parse_address('http://examples.com:9527');

        $this->assertEquals($info, [
            'scheme' => 'http',
            'host' => 'examples.com',
            'port' => '9527',
            'sock' => null
        ]);
    }

    public function testParseUsername()
    {
        $info = parse_url('mysql://127.0.0.1:9527');

        $this->assertEquals($info, [
            'scheme' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '9527',
        ]);

        $info = parse_url('127.0.0.1:9527');

        $this->assertEquals($info, [
            'host' => '127.0.0.1',
            'port' => '9527',
        ]);
    }
}
