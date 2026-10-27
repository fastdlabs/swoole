# Concurrency Examples

## Parallel

```bash
# CGI / 普通 CLI：串行执行
php examples/concurrency/parallel.php

# Swoole：协程并发执行
php examples/concurrency/parallel.php --swoole
```

示例调用 `Parallel::all()`：

- 普通 CLI / CGI 自动串行
- Swoole 协程上下文自动并发
- 同样的任务代码
- 整体超时 0.5s

预期：

```text
CGI:   elapsed_ms ≈ 260
Swoole: elapsed_ms ≈ 120
```
