<?php

declare(strict_types=1);

use FastD\Swoole\Process\Worker;
use FastD\Swoole\Process\IPC\Memory;
use FastD\Swoole\Process\IPC\Queue;
use FastD\Swoole\Process\IPC\Socket;
use Swoole\Event;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * IPC 通信示例 Worker 类
 */
class IPCWorker extends Worker
{
    public function process(Worker $worker): void
    {
        echo "\033[32m[Worker " . getmypid() . "] 启动，等待接收数据...\033[0m" . PHP_EOL;
        
        // 尝试读取数据
        $startTime = time();
        $timeout = 5; // 5秒超时
        
        while (time() - $startTime < $timeout) {
            $data = $this->ipcRead();
            if ($data !== false) {
                echo "\033[34m[Worker " . getmypid() . "] 接收到数据: \033[0m" . PHP_EOL;
                echo "\033[36m" . print_r($data, true) . "\033[0m" . PHP_EOL;
                
                // 处理数据
                $processedData = [
                    'status' => 'processed',
                    'pid' => getmypid(),
                    'original_data' => $data,
                    'processed_at' => date('Y-m-d H:i:s')
                ];
                
                echo "\033[32m[Worker " . getmypid() . "] 数据处理完成，准备响应...\033[0m" . PHP_EOL;
                
                // 向父进程发送处理结果
                $this->ipcWrite($processedData);
                echo "\033[32m[Worker " . getmypid() . "] 响应已发送\033[0m" . PHP_EOL;
                
                break;
            }
            
            // 短暂休眠，避免 CPU 占用过高
            usleep(100000); // 100ms
        }
        
        if (time() - $startTime >= $timeout) {
            echo "\033[31m[Worker " . getmypid() . "] 超时未收到数据\033[0m" . PHP_EOL;
        }
        
        echo "\033[32m[Worker " . getmypid() . "] 工作完成\033[0m" . PHP_EOL;
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
 * 运行 IPC 示例
 */
function runIPCExample(string $ipcType, callable $ipcCreator): void
{
    colorEcho("\n========================================", 'yellow');
    colorEcho("=== $ipcType 通信示例 ===", 'yellow');
    colorEcho("========================================", 'yellow');
    
    // 创建 IPC 实例
    $ipc = $ipcCreator();
    colorEcho("[Parent " . getmypid() . "] 创建了 $ipcType IPC 实例", 'green');
    
    // 创建 Worker 实例
    $worker = new IPCWorker("{$ipcType}Worker", true, $ipc);
    colorEcho("[Parent " . getmypid() . "] 创建了 Worker 实例: {$worker->name}", 'green');
    
    // 监听子进程退出信号
    \Swoole\Process::signal(SIGCHLD, function () use ($worker, $ipcType) {
        while ($ret = \Swoole\Process::wait(false)) {
            $worker->stop($ret['signal'], $ret['code']);
            colorEcho("[Parent " . getmypid() . "] 回收子进程：PID={$ret['pid']}，退出码={$ret['code']}，信号={$ret['signal']}", 'purple');
        }
    });
    
    // 启动进程
    try {
        $pid = $worker->start();
        colorEcho("[Parent " . getmypid() . "] 进程已启动，子进程 PID: $pid", 'green');
        
        // 等待子进程初始化
        usleep(500000); // 500ms
        
        // 准备测试数据
        $testData = [
            'message' => "Hello from parent process!",
            'pid' => getmypid(),
            'timestamp' => time(),
            'data' => [
                'user_id' => 123,
                'action' => 'test_ipc',
                'params' => ['key' => 'value', 'number' => 456]
            ]
        ];
        
        colorEcho("[Parent " . getmypid() . "] 准备发送测试数据:", 'blue');
        colorEcho(print_r($testData, true), 'cyan');
        
        // 向子进程发送数据
        $startTime = microtime(true);
        $success = $worker->ipcWrite($testData);
        $endTime = microtime(true);
        
        if ($success) {
            colorEcho("[Parent " . getmypid() . "] 数据发送成功，耗时: " . round(($endTime - $startTime) * 1000, 2) . "ms", 'green');
        } else {
            colorEcho("[Parent " . getmypid() . "] 数据发送失败", 'red');
        }
        
        // 尝试接收子进程的响应
        colorEcho("[Parent " . getmypid() . "] 等待子进程响应...", 'blue');
        
        $startTime = time();
        $timeout = 3; // 3秒超时
        $response = false;
        
        while (time() - $startTime < $timeout) {
            $response = $worker->ipcRead();
            if ($response !== false) {
                colorEcho("[Parent " . getmypid() . "] 接收到子进程响应:", 'purple');
                colorEcho(print_r($response, true), 'cyan');
                break;
            }
            
            usleep(100000); // 100ms
        }
        
        if ($response === false) {
            colorEcho("[Parent " . getmypid() . "] 未收到子进程响应", 'yellow');
        }
        
        // 等待子进程完成
        sleep(2);
        
    } catch (Exception $e) {
        colorEcho("[Parent " . getmypid() . "] 启动进程失败: " . $e->getMessage(), 'red');
    }
    
    colorEcho("=== $ipcType 通信示例结束 ===", 'yellow');
}

// 主程序
colorEcho("FastD Swoole IPC 通信示例", 'purple');
colorEcho("演示三种 IPC 实现的通信过程", 'purple');

// 运行 Memory 示例
runIPCExample('Memory', function () {
    return new Memory('test_memory', 1024);
});

// 运行 Queue 示例
runIPCExample('Queue', function () {
    return new Queue(ftok(__FILE__, 'q'), SWOOLE_MSGQUEUE_BALANCE);
});

// 运行 Socket 示例
runIPCExample('Socket', function () {
    return new Socket('127.0.0.1', 9501);
});

colorEcho("\n所有 IPC 示例执行完成", 'green');
