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

    /**
     * 进程逻辑处理
     */
    public function handle(): void
    {
        for ($i = 0; $i <= 3; $i++){
            echo $i.PHP_EOL;
            sleep(1);
        }
    }

    /**
     * 进程退出调用
     */
    public function exit(int $pid, int $code, int $signal): void
    {
        $this->start();
    }
}
