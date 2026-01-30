<?php

declare(strict_types=1);

namespace FastD\Swoole\Server\Listener;

use FastD\Swoole\Server\Event\SwooleEvent;
use Swoole\Server;

class WorkerListener extends SwooleEventListener
{
    public function listen(): iterable
    {
        return [
            SwooleEvent::class,
        ];
    }

    public function onStart(SwooleEvent $event): void
    {
        foreach ($event->ports as $portInfo) {
            $protocol = $portInfo['protocol'] ?? 'unknown';
            $host = $portInfo['host'] ?? 'unknown';
            $port = $portInfo['port'] ?? 'unknown';
            echo "[" . date('Y-m-d H:i:s') . "] \033[32m[START]\033[0m Server started at \033[36m{$protocol}://{$host}:{$port}\033[0m\n";
        }
    }

    public function onBeforeShutdown(SwooleEvent $event): void
    {
        foreach ($event->ports as $portInfo) {
            $protocol = $portInfo['protocol'] ?? 'unknown';
            $host = $portInfo['host'] ?? 'unknown';
            $port = $portInfo['port'] ?? 'unknown';
            echo "[" . date('Y-m-d H:i:s') . "] \033[33m[SHUTDOWN]\033[0m Preparing to shutdown server at \033[36m{$protocol}:{$host}{$port}\033[0m Process ID: \033[36m" . getmypid() . "\033[0m\n";
        }
    }

    public function onShutdown(SwooleEvent $event): void
    {
        foreach ($event->ports as $portInfo) {
            $protocol = $portInfo['protocol'] ?? 'unknown';
            $host = $portInfo['host'] ?? 'unknown';
            $port = $portInfo['port'] ?? 'unknown';
            echo "[" . date('Y-m-d H:i:s') . "] \033[31m[MASTER SHUTDOWN]\033[0m Server shutdown completed at \033[36m{$protocol}://{$host}:{$port}\033[0m\n";
        }
    }

    public function onManagerStart(SwooleEvent $event): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] \033[35m[MANAGER]\033[0m Manager process started\n";
    }

    public function onManagerStop(SwooleEvent $event): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] \033[35m[MANAGER]\033[0m Manager process stopped\n";
    }

    public function onWorkerStart(SwooleEvent $event): void
    {
        [$id] = $event->args;
        echo "[" . date('Y-m-d H:i:s') . "] \033[36m[WORKER]\033[0m Worker \033[33m#{$id}\033[0m started\n";
    }

    public function onWorkerStop(SwooleEvent $event): void
    {
        [$id] = $event->args;
        echo "[" . date('Y-m-d H:i:s') . "] \033[36m[WORKER]\033[0m Worker \033[33m#{$id}\033[0m stopped\n";
    }

    public function onWorkerError(SwooleEvent $event): void
    {
        [$id, $workerPid, $exitCode, $signal] = $event->args;
        
        $exitMessage = match ($exitCode) {
            0 => 'Normal exit',
            1 => 'General error',
            2 => 'Misuse of shell command',
            126 => 'Command cannot execute',
            127 => 'Command not found',
            128 => 'Invalid argument to exit',
            130 => 'Script terminated by Control-C',
            132 => 'Illegal instruction',
            133 => 'Trace/breakpoint trap',
            134 => 'Aborted',
            136 => 'Floating point exception',
            137 => 'Killed (often sent by system when OOM)',
            139 => 'Segmentation fault',
            143 => 'Terminated (SIGTERM)',
            default => 'Unknown exit code'
        };

        $signalMessage = match ($signal) {
            0 => 'No signal',
            1 => 'SIGHUP - Hangup (control terminal)',
            2 => 'SIGINT - Interrupt (Ctrl+C)',
            3 => 'SIGQUIT - Quit (Ctrl+\\)',
            6 => 'SIGABRT - Abort signal',
            9 => 'SIGKILL - Kill signal',
            11 => 'SIGSEGV - Invalid memory reference',
            13 => 'SIGPIPE - Broken pipe',
            14 => 'SIGALRM - Timer signal',
            15 => 'SIGTERM - Termination signal',
            default => "Signal {$signal}"
        };

        echo "[" . date('Y-m-d H:i:s') . "] \033[31m[CRITICAL]\033[0m Worker \033[33m#{$id}\033[0m (PID: {$workerPid}) \033[31mUNEXPECTEDLY EXITED\033[0m\n";
        echo "[" . date('Y-m-d H:i:s') . "]          Exit Code: \033[31m{$exitCode}\033[0m (\033[31m{$exitMessage}\033[0m)\n";
        echo "[" . date('Y-m-d H:i:s') . "]          Signal: \033[31m{$signal}\033[0m (\033[31m{$signalMessage}\033[0m)\n";
        foreach ($event->ports as $portInfo) {
            $host = $portInfo['host'] ?? 'unknown';
            $port = $portInfo['port'] ?? 'unknown';
            echo "[" . date('Y-m-d H:i:s') . "]          Server: \033[36m{$host}:{$port}\033[0m\n";
        }
    }

    public function onWorkerExit(SwooleEvent $event): void
    {
        [$id] = $event->args;
        echo "[" . date('Y-m-d H:i:s') . "] \033[35m[EXIT]\033[0m Worker \033[33m#{$id}\033[0m exited normally\n";
    }

    public function onBeforeReload(SwooleEvent $event): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] \033[33m[WORKER BEFORE RELOAD]\033[0m Preparing to reload\n";
    }

    public function onAfterReload(SwooleEvent $event): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] \033[32m[WORKER AFTER RELOAD]\033[0m Workers reloaded successfully\n";
    }
}