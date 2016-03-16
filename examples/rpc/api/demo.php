<?php

/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/3/7
 * Time: 下午5:53
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */
class Demo
{
    public function emptyArg()
    {
        return [
            'name' => 'test22222',
            'content' => [
                'action' => 'emptyArg',
                'msg' => 'empty arg action'
            ]
        ];
    }

    public function arg($gender)
    {}

    public function multiArgs($name, $gender, $age)
    {}

    public static function testStatic()
    {}
}