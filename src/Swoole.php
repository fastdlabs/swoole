<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;


use FastD\Swoole\Client\Sync\TCP;
use FastD\Swoole\Server\Http;
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
    const DEFAULT_COMMAND = 'client';

    public function __construct()
    {
        parent::__construct(Server::SWOOLE, Server::VERSION);

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
            ->setHelp('This command allows you to create swoole client...')
            ->setDescription('Create new swoole client')
        ;

        $this
            ->addArgument('host', InputArgument::REQUIRED, 'Swoole server host address')
            ->addArgument('port', InputArgument::REQUIRED, 'Swoole server port')
            ->addOption('type', 't', InputOption::VALUE_OPTIONAL, 'Swoole server type', 'tcp')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $address = '';
        switch ($input->getOption('type')) {
            case 'http':
                $address .= 'http://';
                $client = Http::class;
                break;
            case 'tcp':
                $address .= 'tcp://';
                $client = TCP::class;
                break;
            case 'udp':
                $client = Udp::class;
                $address .= 'udp://';
                break;
            case 'ws':
                $client = WebSocket::class;
                $address .= 'ws://';
                break;
            default:
                throw new \LogicException('Not support server type ' . $input->getOption('type'));
        }

        $address .= $input->getArgument('host') . ':' . $input->getArgument('port');

        $client = new $client($address);

        $client
            ->connect(function ($client) {
                $client->send('hello world');
            })
            ->receive(function ($client, $data) {
                echo $data . PHP_EOL;
            })
            ->resolve()
        ;
    }
}