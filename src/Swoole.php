<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;


use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Swoole extends Application
{
    public function __construct()
    {
        parent::__construct('swoole', Server::VERSION);

        $this->add(new ServerCommand());
    }
}

class ServerCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('server')
            ->setHelp('This command allows you to create swoole server...')
            ->setDescription('Create new swoole server')
        ;

        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'Swoole server process name', 'swoole server')
            ->addArgument('action', InputArgument::OPTIONAL, 'Swoole status', 'status')
            ->addOption('host', '', InputOption::VALUE_OPTIONAL, 'Swoole server host address', '127.0.0.1')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'Swoole server port', '9527')
            ->addOption('daemon', 'd', InputOption::VALUE_OPTIONAL, 'Swoole server running mode', false)
            ->addOption('type', 't', InputOption::VALUE_OPTIONAL, 'Swoole server type', 'tcp')
            ->addOption('mode', 'm', InputOption::VALUE_OPTIONAL, 'Swoole server run mode', SWOOLE_PROCESS)
            ->addOption('pid', 'pid', InputOption::VALUE_OPTIONAL, 'Swoole server pid no')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

    }
}