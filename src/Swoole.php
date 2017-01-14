<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;


use FastD\Swoole\Server\Http;
use FastD\Swoole\Server\Tcp;
use FastD\Swoole\Server\Udp;
use FastD\Swoole\Server\WebSocket;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Swoole extends Application
{
    const DEFAULT_COMMAND = 'server';

    public function __construct()
    {
        parent::__construct('swoole', Server::VERSION);

        $this->add(new ServerCommand());
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $argv = $_SERVER['argv'];

        $script = array_shift($argv);

        array_unshift($argv, static::DEFAULT_COMMAND);
        array_unshift($argv, $script);

        return parent::run(new ArgvInput($argv), $output);
    }
}

class ServerCommand extends Command
{
    public function configure()
    {
        $this
            ->setName(Swoole::DEFAULT_COMMAND)
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
        $address = '';
        $server  = '';
        switch ($input->getOption('type')) {
            case 'http':
                $address .= 'http://';
                $server = Http::class;
                break;
            case 'tcp':
                $address .= 'tcp://';
                $server = Tcp::class;
                break;
            case 'udp':
                $server = Udp::class;
                $address .= 'udp://';
                break;
            case 'ws':
                $server = WebSocket::class;
                $address .= 'ws://';
                break;
            default:
                throw new \LogicException('Not support server type ' . $input->getOption('type'));
        }

        $address .= $input->getOption('host') . ':' . $input->getOption('port');

        $server = $server::createServer($input->getArgument('name'), $address);

        switch ($input->getArgument('action')) {
            case 'start':
                $server->start();
                break;
            case 'stop':
                break;
            case 'status':
                break;
            case 'reload':
                break;
            case 'restart':
                break;
            default:
                throw new \LogicException(sprintf('Not support action ' . $input->getArgument('action')));
        }
    }
}