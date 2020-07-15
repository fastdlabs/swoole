<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

include __DIR__ . '/../../vendor/autoload.php';

class P extends \FastD\Swoole\Process\Process{

    public function handle(): void
    {
        for ($i = 0; $i <= 10; $i++){
            echo $i.PHP_EOL;
            sleep(1);
        }
    }
}

$p = new P('test');

$p->start();
