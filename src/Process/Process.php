<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Process;


use Swoole\Process as Swoole;

abstract class Process
{
    protected string $name;

    protected bool $stdout = false;
    protected int $pipe = SOCK_DGRAM;
    protected bool $coroutine = false;

    protected Swoole $process;

    protected int $pid;

    protected array $children = [];

    public function __construct(string $name, bool $redirect_stdin_stdout = false, int $pipe_type = SOCK_DGRAM, bool $enable_coroutine = false)
    {
        $this->process = new Swoole([$this, 'handle'], $redirect_stdin_stdout, $pipe_type, $enable_coroutine);
        $this->stdout = $redirect_stdin_stdout;
        $this->pid = $pipe_type;
        $this->coroutine = $enable_coroutine;
        $this->name($name);
    }

    public function name(string $name) {
        $this->name = $name;
//        $this->process->name($name);
        return $this;
    }

    public function daemon(): Process
    {
        $this->process->daemon();

        return $this;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function fork(int $worker_num)
    {
        for ($i = 0; $i < $worker_num; $i++) {
            $process = new static($this->name . ' worker', $this->stdout, $this->pipe, $this->coroutine);
            $process->start(false);
            $this->children[$process->getPid()] = $process;
        }
        while($ret = Swoole::wait()){
            $this->exit($ret['pid'], $ret['code'], $ret['signal']);
        }
    }

    public function start(bool $wait = true): int
    {
        $this->pid = $this->process->start();

        if ($wait) {
            $ret = Swoole::wait(true);
            $this->exit($ret['pid'], $ret['code'], $ret['signal']);
        }

        return $this->pid;
    }

    public function recv(): string
    {
        $socket = $this->process->exportSocket();
        return $socket->recv();
    }

    public function send($data): void
    {
        $socket = $this->process->exportSocket();
        if (false != $socket) {
            $socket->send($data);
        }
    }

    public function close(int $which): bool
    {
        return $this->process->close($which);
    }

    abstract public function handle(): void;

    abstract public function exit(int $pid, int $code, int $signal): void;
}
