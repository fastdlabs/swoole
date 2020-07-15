<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

use FastD\Swoole\Process\Process;

include __DIR__ . '/../../vendor/autoload.php';

class P extends Process{

    public function handle(): void
    {
        for ($i = 0; $i <= 3; $i++){
            echo $i.PHP_EOL;
            sleep(1);
        }
    }

    public function exit(int $pid, int $code, int $signal): void
    {
        print_r(func_get_args());
    }
}
