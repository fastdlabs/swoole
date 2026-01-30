<?php

declare(strict_types=1);

use FastD\Swoole\Process\Worker;
use Swoole\Event;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * 简单的工作进程示例
 */
class SimpleWorker extends Worker
{
    public function process(Worker $worker): void
    {
        echo "[Worker " . getmypid() . "] 开始工作..." . PHP_EOL;
        echo "子进程[{$worker->pid}]启动\n";
        // 模拟一些工作
        for ($i = 1; $i <= 5; $i++) {
            echo "[Worker " . getmypid() . "] 处理任务 {$i}/5" . PHP_EOL;
            if ($i == 4) {
                throw new Exception('tset');
            }
            sleep(1);
        }

        echo "[Worker " . getmypid() . "] 工作完成" . PHP_EOL;
    }
}

echo "=== Swoole Process 简单示例 ===" . PHP_EOL;
$worker = new SimpleWorker('SimpleWorker');
// 监听子进程退出信号 SIGCHLD（子进程退出会触发该信号）
\Swoole\Process::signal(SIGCHLD, function () use ($worker) {
    // 非阻塞回收：循环调用，确保回收所有已退出的子进程
    while ($ret = \Swoole\Process::wait(false)) {
        $worker->stop($ret['signal'], $ret['code']);
        echo "回收子进程：PID={$ret['pid']}，退出码={$ret['code']}，信号={$ret['signal']}\n";
    }
});

// 创建工作进程

print_r($worker);

echo "创建进程: {$worker->name}" . PHP_EOL;

// 启动进程并等待完成
try {
    $worker->start();
    echo "进程已启动，PID: ".getmypid()."" . PHP_EOL;
    echo "等待进程完成..." . PHP_EOL;
} catch (Exception $e) {
    echo "启动进程失败: " . $e->getMessage() . PHP_EOL;
}

// 使用Swoole定时器延时10秒后退出
\Swoole\Timer::after(5000, function() use ($worker) {

    echo "10秒延时结束，程序即将退出\n";
});

Event::wait();

print_r($worker);
echo "示例执行完毕" . PHP_EOL;


