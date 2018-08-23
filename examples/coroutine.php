<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

go(function () {
    co::sleep(0.5);
    echo "hello";
});

class A
{
    public function method()
    {
        echo 'method';
    }
}
go([new A, "method"]);

$c = new chan(1);
$c->push('a');
$c->pop();