# Process Examples

Process 采用两层设计：

- `Worker`：单个工作进程的启动、停止、状态、管道与 IPC
- `Process`：worker-manager，负责创建、回收、转发信号和状态

## 示例

### 单 Worker 生命周期

```bash
php examples/process/worker_once.php
```

展示：

- `Worker::start()` 启动子进程
- 子进程执行 `process()`
- 父进程等待并回收
- `Worker::getPid()` 在未启动时返回 `0`

### Worker Manager

```bash
php examples/process/manager.php
```

展示：

- 一个 `Process` 管理多个 `Worker`
- 子进程退出回收
- `Ctrl+C` 触发 manager 停止所有 worker
- 状态更新

## 设计边界

当前 `Process` 不负责：

- 自动重启失败 worker
- 复杂健康检查
- 任务分发队列

这些属于业务层或上层调度器。`Process` 只保持进程编排和生命周期管理。

## 注意

- 示例需要在支持 `pcntl` / Swoole Process 的 Linux 环境运行
- 建议在终端中运行，`Ctrl+C` 退出
