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
        $info = parse_url('http://examples.com:9527');

        $this->assertEquals([
            'scheme' => 'http',
            'host' => 'examples.com',
            'port' => '9527'
        ], $info);
    }

    public function testSocketType()
    {
        $info = parse_url('tcp://examples.com:9527');

        $this->assertEquals([
            'scheme' => 'tcp',
            'host' => 'examples.com',
            'port' => '9527'
        ], $info);
    }
}
