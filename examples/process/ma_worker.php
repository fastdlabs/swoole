<?php

declare(strict_types=1);

use FastD\Swoole\Event\Process\QuitEvent;
use FastD\Swoole\Event\Process\SignalEvent;
use FastD\Swoole\Event\SwooleEvent;
use FastD\Swoole\Listener\Process\SignalEventListener;
use FastD\Swoole\Listener\SwooleEventListener;
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
        echo "    [Children Worker " . getmypid() . "] 开始工作..." . PHP_EOL;
        echo "    子进程[{$worker->process->pid}]启动\n";
        // 模拟一些工作
        for ($i = 1; $i <= 3; $i++) {
            echo "        [Children Worker " . getmypid() . "] 处理任务 {$i}/5" . PHP_EOL;
            sleep(1);
        }

        echo "    [Worker " . getmypid() . "] 工作完成" . PHP_EOL;
    }
}
class SimpleWorker2 extends Worker
{
    public function process(Worker $worker): void
    {
        echo "    [Children Worker " . getmypid() . "] 开始工作..." . PHP_EOL;
        echo "    子进程[{$worker->process->pid}]启动\n";
        // 模拟一些工作
        for ($i = 1; $i <= 7; $i++) {
            echo "        [Children Worker " . getmypid() . "] 处理任务 {$i}/7" . PHP_EOL;
            sleep(1);
        }

        echo "    [Children Worker " . getmypid() . "] 工作完成" . PHP_EOL;
    }
}
class SimpleWorker3 extends Worker
{
    public function process(Worker $worker): void
    {
        echo "    [Children Worker " . getmypid() . "] 开始工作..." . PHP_EOL;
        echo "    子进程[{$worker->process->pid}]启动\n";
        // 模拟一些工作
        for ($i = 1; $i <= 3; $i++) {
            echo "        [Children Worker " . getmypid() . "] 处理任务 {$i}/7" . PHP_EOL;
            sleep(1);
            if ($i == 2) {
                throw new Exception('test');
            }
        }

        echo "    [Children Worker " . getmypid() . "] 工作完成" . PHP_EOL;
    }
}

class ConcreteSignalEventListener extends SignalEventListener
{
    protected function onUserSignal(object $event): void
    {
        // 处理用户自定义信号
        echo "接收到用户自定义信号: " . ($event->event ?? 'unknown') . "\n";
        if (isset($event->args['pid'])) {
            echo "PID: " . $event->args['pid'] . "\n";
        }
        if (isset($event->args['code'])) {
            echo "Code: " . $event->args['code'] . "\n";
        }
        if (isset($event->args['signal'])) {
            echo "Signal: " . $event->args['signal'] . "\n";
        }
    }
}

class CustomSignalEventListener extends SwooleEventListener
{
    public function listen(): iterable
    {
        return [
            QuitEvent::class,
        ];
    }

    public function process(object $event): void
    {
        echo "[CustomSignalListener] 收到信号事件: " . $event->event . "\n";
        echo "[CustomSignalListener] 信号编号: " . $event->signo . "\n";
        echo "[CustomSignalListener] 对象类型: " . get_class($event->object) . "\n";
        if (isset($event->args['pid'])) {
            echo "[CustomSignalListener] PID: " . $event->args['pid'] . "\n";
        }
        if (isset($event->args['code'])) {
            echo "[CustomSignalListener] 退出码: " . $event->args['code'] . "\n";
        }
    }
}

$ma = new \FastD\Swoole\Process();
$ma->addListener(new ConcreteSignalEventListener());
$ma->addListener(new CustomSignalEventListener());
$ma->addWorker(
     new SimpleWorker('simple-worker'),
     new SimpleWorker2('simple-worker2'),
     new SimpleWorker3('simple-worker3')
);
$ma->start();
