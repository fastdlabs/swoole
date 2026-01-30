# IPC 设计与 Worker 整合方案

## 一、当前问题分析

### 1. 命名空间不一致
- Memory、Queue、Socket 类使用 `FastD\Swoole\Process\Communication` 命名空间
- IPCInterface 和 IPC 抽象类使用 `FastD\Swoole\Process\IPC` 命名空间

### 2. 接口设计问题
- IPCInterface 缺少 `init()` 方法，与 IPC 抽象类要求不一致
- 循环依赖：IPC 构造函数依赖 Worker 实例，而 Worker 可接收 IPCInterface

### 3. 实现问题
- Memory 类使用未定义常量 `IPC_SHARED_MEMORY`
- 初始化不一致：Memory 在构造函数中自动初始化，其他实现需要手动调用 `init()`
- `getIPCType()` 方法返回值不一致：Memory 返回未定义常量，Queue 返回 msgKey，Socket 返回 type

### 4. Worker 整合问题
- 只判断了 Queue 类型的通信，未考虑其他 IPC 实现
- 管道类型设置不完整
- 缺少统一的 IPC 初始化逻辑
- 直接使用不一致的 `getIPCType()` 返回值

## 二、解决方案设计

### 1. 命名空间与接口统一
- **统一命名空间**：所有 IPC 实现使用 `FastD\Swoole\Process\IPC` 命名空间
- **完善接口**：在 IPCInterface 中添加 `init()` 方法
- **解决循环依赖**：移除 IPC 构造函数对 Worker 的依赖，改为通过方法参数传递

### 2. 常量与类型标准化
- **定义统一常量**：在 IPC 抽象类中定义标准 IPC 类型常量
  - `const IPC_TYPE_MEMORY = 1;`
  - `const IPC_TYPE_QUEUE = 2;`
  - `const IPC_TYPE_SOCKET = 3;`
- **统一 getIPCType() 实现**：所有实现返回对应的常量值

### 3. 初始化与生命周期管理
- **统一初始化流程**：所有 IPC 实现移除构造函数中的自动初始化，改为显式调用 `init()`
- **Worker 整合初始化**：在 Worker 的 `bootstrap()` 方法中统一初始化 IPC 实例
- **标准化关闭流程**：确保所有 IPC 实现正确处理资源释放

### 4. Worker 整合优化
- **完善通信类型判断**：支持所有 IPC 实现类型
- **统一管道类型设置**：根据 IPC 类型设置合适的管道类型
- **优化 Swoole\Process 创建**：根据 IPC 类型和配置创建合适的进程实例
- **添加 IPC 访问方法**：在 Worker 中添加便捷的 IPC 读写方法

### 5. Swoole 特性兼容
- **共享内存**：使用 Swoole\Table，确保在进程启动前创建
- **消息队列**：使用 Swoole\Process::useQueue()，在合适时机初始化
- **套接字**：使用 Swoole\Coroutine\Socket，支持协程环境
- **进程管理**：遵循 Swoole\Process 的生命周期管理

## 三、具体实现步骤

### 1. 修正 IPCInterface.php
- 添加 `public function init(): bool;` 方法

### 2. 修正 IPC.php
- 移除构造函数中的 Worker 依赖
- 添加 IPC 类型常量
- 调整构造函数参数，只传递必要配置

### 3. 修正 Memory.php
- 修正命名空间
- 使用统一的 IPC_TYPE_MEMORY 常量
- 移除构造函数中的自动初始化
- 调整构造函数参数

### 4. 修正 Queue.php
- 修正命名空间
- 使用统一的 IPC_TYPE_QUEUE 常量
- 调整 getIPCType() 方法返回值

### 5. 修正 Socket.php
- 修正命名空间
- 使用统一的 IPC_TYPE_SOCKET 常量
- 调整 getIPCType() 方法返回值

### 6. 优化 Worker.php
- 完善 bootstrap() 方法中的 IPC 类型判断
- 统一初始化 IPC 实例
- 添加便捷的 IPC 访问方法
- 优化 Swoole\Process 创建逻辑

## 四、使用示例

### 1. 共享内存通信示例
```php
$memoryIPC = new Memory('shared_memory', 1024);
$worker = new MyWorker('memory_worker', true, $memoryIPC);
$worker->start();
// 在进程中使用
$worker->getIPC()->write(['key' => 'value']);
$data = $worker->getIPC()->read();
```

### 2. 消息队列通信示例
```php
$queueIPC = new Queue(ftok(__FILE__, 'a'), SWOOLE_MSGQUEUE_BALANCE);
$worker = new MyWorker('queue_worker', true, $queueIPC);
$worker->start();
// 在进程中使用
$worker->getIPC()->write(['message' => 'hello']);
$data = $worker->getIPC()->read();
```

### 3. 套接字通信示例
```php
$socketIPC = new Socket('127.0.0.1', 9501);
$worker = new MyWorker('socket_worker', true, $socketIPC);
$worker->start();
// 在进程中使用
$worker->getIPC()->write(['data' => 'test']);
$data = $worker->getIPC()->read();
```

## 五、预期效果

1. **统一接口**：所有 IPC 实现遵循相同的接口规范
2. **简化使用**：Worker 自动管理 IPC 初始化和生命周期
3. **类型安全**：使用统一的 IPC 类型常量，避免类型错误
4. **性能优化**：根据不同 IPC 类型选择最优的通信方式
5. **可扩展性**：易于添加新的 IPC 实现
6. **Swoole 兼容**：充分利用 Swoole 扩展的特性，同时遵循其约束

此方案将解决当前的设计问题，提供清晰、统一的 IPC 使用体验，并与 Worker 类无缝整合。