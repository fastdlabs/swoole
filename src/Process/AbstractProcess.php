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

abstract class AbstractProcess
{
    protected string $name;

    protected bool $stdout = false;
    protected int $pipe = SOCK_DGRAM;
    protected bool $coroutine = false;

    protected Swoole $process;

    protected int $pid;

    protected array $children = [];

    public function __construct(string $name, bool $redirectStdinStdout = false, int $pipeType = SOCK_DGRAM, bool $enableCoroutine = false)
    {
        $this->process = new Swoole([$this, 'handle'], $redirectStdinStdout, $pipeType, $enableCoroutine);
        $this->stdout = $redirectStdinStdout;
        $this->pid = $pipeType;
        $this->coroutine = $enableCoroutine;
        $this->name($name);
    }

    public function name(string $name) {
        $this->name = $name;
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

    public function daemon(): AbstractProcess
    {
        $this->process->daemon();

        return $this;
    }

    public function affinity(array $cpus): AbstractProcess
    {
        $this->process->setAffinity($cpus);

        return $this;
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
        if (false !== $socket = $this->process->exportSocket()) {
            return $socket->recv();
        }
        return '';
    }

    public function send($data): void
    {
        if (false !== $socket = $this->process->exportSocket()) {
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
