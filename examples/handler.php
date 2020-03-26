<?php

use FastD\Swoole\Handlers\HandlerInterface;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

class Handler implements HandlerInterface {
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
}