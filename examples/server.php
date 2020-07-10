<?php

use FastD\Swoole\TCP;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

class Server extends TCP {

    /**
     * @inheritDoc
     */
    public function onStart(\Swoole\Server $server): bool
    {
        // TODO: Implement onStart() method.
    }

    /**
     * @inheritDoc
     */
    public function onShutdown(\Swoole\Server $server): bool
    {
        // TODO: Implement onShutdown() method.
    }

    /**
     * @inheritDoc
     */
    public function onManagerStart(\Swoole\Server $server): bool
    {
        // TODO: Implement onManagerStart() method.
    }

    /**
     * @inheritDoc
     */
    public function onManagerStop(\Swoole\Server $server): bool
    {
        // TODO: Implement onManagerStop() method.
    }

    /**
     * @inheritDoc
     */
    public function onWorkerStart(\Swoole\Server $server, int $id): bool
    {
        // TODO: Implement onWorkerStart() method.
    }

    /**
     * @inheritDoc
     */
    public function onWorkerStop(\Swoole\Server $server, int $id): bool
    {
        // TODO: Implement onWorkerStop() method.
    }

    /**
     * @inheritDoc
     */
    public function onWorkerError(
        \Swoole\Server $server,
        int $worker_id,
        int $worker_pid,
        int $exit_code,
        int $signal
    ): bool {
        // TODO: Implement onWorkerError() method.
    }

    /**
     * @inheritDoc
     */
    public function onWorkerExit(\Swoole\Server $server, int $id): bool
    {
        // TODO: Implement onWorkerExit() method.
    }

    /**
     * @inheritDoc
     */
    public function onClose(\Swoole\Server $server, int $fd, int $id): bool
    {
        // TODO: Implement onClose() method.
    }

    /**
     * @inheritDoc
     */
    public function onPipeMessage(\Swoole\Server $server, int $src_worker_id, $message): bool
    {
        // TODO: Implement onPipeMessage() method.
    }

    /**
     * @inheritDoc
     */
    public function config(array $config): void
    {
        // TODO: Implement config() method.
    }

    /**
     * @inheritDoc
     */
    public function on(string $event, object $handle): \FastD\Swoole\Server\ServerInterface
    {
        // TODO: Implement on() method.
    }

    public function close(): bool
    {
        // TODO: Implement close() method.
    }

    public function send(): bool
    {
        // TODO: Implement send() method.
    }

    public function pipeline(): bool
    {
        // TODO: Implement pipeline() method.
    }

    public function check(): bool
    {
        // TODO: Implement check() method.
    }

    public function task(): int
    {
        // TODO: Implement task() method.
    }

    public function finish(): int
    {
        // TODO: Implement finish() method.
    }

    /**
     * @inheritDoc
     */
    public function handle(\FastD\Swoole\Handlers\HandlerInterface $handler): \FastD\Swoole\Server\ServerInterface
    {
        // TODO: Implement handle() method.
    }

    /**
     * @inheritDoc
     */
    public function onReceive(\Swoole\Server $server, int $fd, int $reactor_id, string $data): void
    {
        // TODO: Implement onReceive() method.
    }
}


