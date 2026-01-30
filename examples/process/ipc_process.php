<?php

declare(strict_types=1);

use FastD\Swoole\Process;
use FastD\Swoole\Process\Worker;
use FastD\Swoole\Process\IPC\Queue;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * 数据写入 Worker 类
 */
class WriterWorker extends Worker
{
    public function process(Worker $worker): void
    {
        echo "\033[32m[WriterWorker " . getmypid() . "] 启动，准备写入数据...\033[0m" . PHP_EOL;
        
        // 准备测试数据
        $testData = [
            'type' => 'test_data',
            'pid' => getmypid(),
            'timestamp' => time(),
            'data' => [
                'user_id' => 123,
                'username' => 'test_user',
                'email' => 'test@example.com',
                'score' => 95,
                'tags' => ['php', 'swoole', 'ipc']
            ]
        ];
        
        // 写入数据
        echo "\033[32m[WriterWorker " . getmypid() . "] 写入测试数据...\033[0m" . PHP_EOL;
        echo "\033[36m" . print_r($testData, true) . "\033[0m" . PHP_EOL;
        
        $success = $this->ipcWrite($testData);
        if ($success) {
            echo "\033[32m[WriterWorker " . getmypid() . "] 数据写入成功\033[0m" . PHP_EOL;
        } else {
            echo "\033[31m[WriterWorker " . getmypid() . "] 数据写入失败\033[0m" . PHP_EOL;
        }
        
        // 等待一段时间，确保数据被读取
        sleep(3);
        
        echo "\033[32m[WriterWorker " . getmypid() . "] 任务完成，退出\033[0m" . PHP_EOL;
    }
}

/**
 * 数据读取 Worker 类
 */
class ReaderWorker extends Worker
{
    public function process(Worker $worker): void
    {
        echo "\033[34m[ReaderWorker " . getmypid() . "] 启动，准备读取数据...\033[0m" . PHP_EOL;
        
        // 尝试读取数据
        $startTime = time();
        $timeout = 10; // 10秒超时
        $dataRead = false;
        
        while (time() - $startTime < $timeout) {
            $data = $this->ipcRead();
            if ($data !== false) {
                echo "\033[34m[ReaderWorker " . getmypid() . "] 读取到数据: \033[0m" . PHP_EOL;
                echo "\033[36m" . print_r($data, true) . "\033[0m" . PHP_EOL;
                $dataRead = true;
                break;
            }
            
            // 短暂休眠，避免 CPU 占用过高
            usleep(100000); // 100ms
        }
        
        if (!$dataRead) {
            echo "\033[31m[ReaderWorker " . getmypid() . "] 超时未读取到数据\033[0m" . PHP_EOL;
        } else {
            echo "\033[34m[ReaderWorker " . getmypid() . "] 数据读取完成\033[0m" . PHP_EOL;
        }
        
        echo "\033[34m[ReaderWorker " . getmypid() . "] 任务完成，退出\033[0m" . PHP_EOL;
    }
}

/**
 * 数据报告 Worker 类
 */
class ReporterWorker extends Worker
{
    public function process(Worker $worker): void
    {
        echo "\033[35m[ReporterWorker " . getmypid() . "] 启动，准备向主进程报告数据...\033[0m" . PHP_EOL;
        
        // 准备报告数据
        $reportData = [
            'type' => 'report',
            'pid' => getmypid(),
            'timestamp' => time(),
            'status' => 'completed',
            'processes' => [
                'writer' => 'completed',
                'reader' => 'completed',
                'reporter' => 'active'
            ],
            'stats' => [
                'memory_usage' => memory_get_usage() / 1024 / 1024 . ' MB',
                'uptime' => 0
            ]
        ];
        
        // 向主进程写入报告数据
        echo "\033[35m[ReporterWorker " . getmypid() . "] 向主进程写入报告数据...\033[0m" . PHP_EOL;
        echo "\033[36m" . print_r($reportData, true) . "\033[0m" . PHP_EOL;
        
        $success = $this->ipcWrite($reportData);
        if ($success) {
            echo "\033[35m[ReporterWorker " . getmypid() . "] 报告数据写入成功\033[0m" . PHP_EOL;
        } else {
            echo "\033[31m[ReporterWorker " . getmypid() . "] 报告数据写入失败\033[0m" . PHP_EOL;
        }
        
        // 等待一段时间，确保主进程读取数据
        sleep(2);
        
        echo "\033[35m[ReporterWorker " . getmypid() . "] 任务完成，退出\033[0m" . PHP_EOL;
    }
}

/**
 * 彩色输出函数
 */
function colorEcho(string $message, string $color = 'default'): void
{
    $colors = [
        'default' => "\033[0m",
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'purple' => "\033[35m",
        'cyan' => "\033[36m",
        'white' => "\033[37m",
    ];
    
    $colorCode = $colors[$color] ?? $colors['default'];
    echo $colorCode . $message . $colors['default'] . PHP_EOL;
}

/**
 * 主程序
 */
function main()
{
    colorEcho("========================================", 'yellow');
    colorEcho("=== IPC 进程管理示例 ===", 'yellow');
    colorEcho("========================================", 'yellow');
    
    // 创建 IPC 实例（使用 Queue 类型，适合多进程通信）
    $ipc = new Queue(ftok(__FILE__, 'p'), SWOOLE_MSGQUEUE_BALANCE);
    colorEcho("[Main " . getmypid() . "] 创建了 Queue IPC 实例", 'green');
    
    // 创建 Process 实例
    $process = new Process(enableCoroutine: true, communication: $ipc);
    colorEcho("[Main " . getmypid() . "] 创建了 Process 实例", 'green');
    
    // 创建 Worker 实例
    $workers = [
        new WriterWorker('writer_worker', true, $ipc),
        new ReaderWorker('reader_worker', true, $ipc),
        new ReporterWorker('reporter_worker', true, $ipc)
    ];
    
    // 添加 Worker 到 Process
    foreach ($workers as $worker) {
        $process->addWorker($worker);
        colorEcho("[Main " . getmypid() . "] 添加了 Worker: {$worker->name}", 'green');
    }
    
    // 启动 Process
    colorEcho("[Main " . getmypid() . "] 启动 Process...", 'green');
    $pid = $process->start();
    colorEcho("[Main " . getmypid() . "] Process 已启动，PID: $pid", 'green');
    
    // 等待子进程完成任务
    colorEcho("[Main " . getmypid() . "] 等待子进程执行任务...", 'blue');
    sleep(5);
    
    // 读取 ReporterWorker 写入的数据
    colorEcho("\n[Main " . getmypid() . "] 读取 ReporterWorker 报告数据...", 'blue');
    
    $startTime = time();
    $timeout = 5; // 5秒超时
    $reportReceived = false;
    
    while (time() - $startTime < $timeout) {
        $reportData = $process->ipcRead();
        if ($reportData !== false) {
            colorEcho("[Main " . getmypid() . "] 接收到报告数据: \033[0m", 'purple');
            colorEcho(print_r($reportData, true), 'cyan');
            $reportReceived = true;
            break;
        }
        
        // 短暂休眠，避免 CPU 占用过高
        usleep(100000); // 100ms
    }
    
    if (!$reportReceived) {
        colorEcho("[Main " . getmypid() . "] 未接收到报告数据", 'yellow');
    } else {
        colorEcho("[Main " . getmypid() . "] 报告数据读取完成", 'green');
    }
    
    // 等待所有进程退出
    colorEcho("\n[Main " . getmypid() . "] 等待所有进程退出...", 'green');
    sleep(3);
    
    colorEcho("\n========================================", 'yellow');
    colorEcho("=== IPC 进程管理示例结束 ===", 'yellow');
    colorEcho("========================================", 'yellow');
}

// 运行主程序
main();
