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

    protected Swoole $process;

    public function __construct(string $name, bool $redirect_stdin_stdout = false, int $pipe_type = SOCK_DGRAM, bool $enable_coroutine = false)
    {
        $this->process = new Swoole([$this, 'handle'], $redirect_stdin_stdout, $pipe_type, $enable_coroutine);

        $this->process->name($name);
    }

    abstract public function handle(): void;

    public function daemon(): Process
    {
        $this->process->daemon();

        return $this;
    }

    public function fork(int $worker_num)
    {
        for ($i = 0; $i < $worker_num; $i++) {
            $process = clone $this;
            $process->start();
        }
    }

    public function start(): void
    {
        $this->process->start();

        Swoole::wait(true);
    }

    public function exit(int $status = 0): void
    {

    }
}
