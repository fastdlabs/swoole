<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;


use FastD\Swoole\Console\ClientCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Swoole
 * @package FastD\Swoole
 */
class Swoole extends Application
{
    /**
     *
     */
    const DEFAULT_COMMAND = 'client';

    /**
     * Swoole constructor.
     */
    public function __construct()
    {
        parent::__construct(Server::NAME, Server::VERSION);

        $this->add(new ClientCommand());
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