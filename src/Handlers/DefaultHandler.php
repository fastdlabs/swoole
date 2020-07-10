<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

namespace FastD\Swoole\Handlers;


use Symfony\Component\Console\Output\OutputInterface;

class DefaultHandler implements TCPHandlerInterface
{
    /**
     * Base start handle. Storage process id.
     *
     * @param Server $server
     * @return void
     */
    public function onStart(Server $server): void
    {
        if (version_compare(SWOOLE_VERSION, '1.9.5', '<')) {
            file_put_contents($this->pid_file, $server->master_pid);
            $this->pid = $server->master_pid;
        }

        process_rename($this->name . ' master');

        $this->output->writeln(sprintf("Listen: <info>%s://%s:%s</info>", $this->getProtocol(), $this->getHost(), $this->getPort()));
        foreach ($this->listens as $listen) {
            $this->output->writeln(sprintf(" <info> ></info> Listen: <info>%s://%s:%s</info>", $listen->getProtocol(), $listen->getHost(), $listen->getPort()));
        }

        $this->output->writeln(sprintf('PID file: <info>%s</info>, PID: <info>%s</info>', $this->pid_file, $server->master_pid));
        $this->output->writeln(sprintf('Server Master[<info>%s</info>] is started', $server->master_pid), OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * Shutdown server process.
     *
     * @param Server $server
     * @return void
     */
    public function onShutdown(Server $server): void
    {
        if (file_exists($this->pid_file)) {
            unlink($this->pid_file);
        }

        $this->output->writeln(sprintf('Server <info>%s</info> Master[<info>%s</info>] is shutdown ', $this->name, $server->master_pid), OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * @param Server $server
     *
     * @return void
     */
    public function onManagerStart(Server $server): void
    {
        process_rename($this->getName() . ' manager');

        $this->output->writeln(sprintf('Server Manager[<info>%s</info>] is started', $server->manager_pid), OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * @param Server $server
     *
     * @return void
     */
    public function onManagerStop(Server $server): void
    {
        $this->output->writeln(sprintf('Server <info>%s</info> Manager[<info>%s</info>] is shutdown.', $this->name, $server->manager_pid), OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * @param Server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStart(Server $server, int $worker_id): void
    {
        $worker_name = $server->taskworker ? 'task' : 'worker';
        process_rename($this->getName() . ' ' . $worker_name);
        $this->output->write(sprintf('Server %s[<info>%s</info>] is started [<info>%s</info>]', ucfirst($worker_name), $server->worker_pid, $worker_id) . PHP_EOL);
    }

    /**
     * @param Server $server
     * @param int $worker_id
     * @return void
     */
    public function onWorkerStop(Server $server, int $worker_id): void
    {
        $this->output->writeln(sprintf('Server <info>%s</info> Worker[<info>%s</info>] is shutdown', $this->name, $worker_id), OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * @param Server $server
     * @param $workerId
     * @param $workerPid
     * @param $code
     */
    public function onWorkerError(Server $server, int $workerId, int $workerPid, int $code): void
    {
        $this->output->writeln(sprintf('Server <info>%s:%s</info> Worker[<info>%s</info>] error. Exit code: [<question>%s</question>]', $this->name, $workerPid, $workerId, $code), OutputInterface::VERBOSITY_DEBUG);
    }
}
