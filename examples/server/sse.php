<?php

declare(strict_types=1);

use FastD\Http\Response\SseResponse;
use FastD\Http\Sse\EmitterInterface;
use FastD\Http\Sse\Event;
use FastD\Swoole\Listener\Server\RequestListener;
use FastD\Swoole\Server;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Coroutine;

include __DIR__ . '/../../vendor/autoload.php';

/**
 * 独立 SSE 示例服务器（curl 即可验证，无需应用骨架）：
 *
 *   php examples/server/sse.php
 *   curl -N 'http://127.0.0.1:9531/sse?limit=5&interval=500'
 *   curl -N -H 'Last-Event-ID: 3' 'http://127.0.0.1:9531/sse?limit=5'
 */
class SseExampleListener extends RequestListener
{
    public function onRequest(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $query = $serverRequest->getQueryParams();
        $limit = max(1, min(50, (int) ($query['limit'] ?? 5)));
        $interval = max(0, min(5000, (int) ($query['interval'] ?? 500)));
        $lastId = (int) ($query['last_id'] ?? $serverRequest->getHeaderLine('Last-Event-ID'));
        $start = max(0, $lastId);

        return new SseResponse(function (EmitterInterface $emitter) use ($start, $limit, $interval): void {
            $emitter->comment('connected');

            for ($i = $start + 1; $i <= $start + $limit; $i++) {
                if ($emitter->isClosed()) {
                    return;
                }

                $emitter->send(Event::data(['seq' => $i, 'time' => date('H:i:s')], 'tick', (string) $i));

                if ($interval > 0) {
                    Coroutine::sleep($interval / 1000);
                }
            }

            $emitter->send(Event::data(['message' => 'done'], 'done'));
        }, [
            'heartbeat' => 5,
            'retry' => 3000,
            'max_duration' => 120,
        ]);
    }
}

$server = new Server([
    // SSE 前提：开启协程（write 需要协程），关闭 HTTP 压缩（否则分块被压缩缓冲）
    'enable_coroutine' => true,
    'http_compression' => false,
    'log_level' => 5,
]);

$server->listen('127.0.0.1', 9531, new SseExampleListener());
$server->start();
