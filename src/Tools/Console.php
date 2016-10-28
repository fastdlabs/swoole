<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Tools;

use FastD\Swoole\Watch\Watcher;
use swoole_process;

/**
 * Service 管理脚本
 *
 * Class Service
 *
 * @package FastD\Swoole\Console
 */
trait Console
{
    /**
     * @param $name
     */
    public function rename($name)
    {
        // hidden Mac OS error。
        set_error_handler(function () {});

        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($name);
        } else if (function_exists('swoole_set_process_name')) {
            swoole_set_process_name($name);
        }

        restore_error_handler();
    }

    /**
     * Return process pid.
     *
     * @return int
     */
    abstract public function getPid();

    /**
     * Driver swoole app.
     *
     * @return mixed
     */
    abstract public function bootstrap();

    /**
     * @return \swoole_server
     */
    abstract public function getSwoole();

    /**
     * @return string
     */
    abstract public function getServerName();

    /**
     * @return bool
     */
    protected function isRunning()
    {
        $processName = $this->getServerName();

        if ('Linux' !== PHP_OS) {
            $processName = $_SERVER['SCRIPT_NAME'];
        }
        // | awk '{print $1, $2, $6, $8, $9, $11, $12}'
        exec("ps axu | grep '{$processName}' | grep -v grep", $output);

        if (empty($output)) {
            return false;
        }

        $output = array_map(function ($v) {
            $status = preg_split('/\s+/', $v);

            unset($status[2], $status[3], $status[4], $status[6], $status[9]); //

            $status = array_values($status);

            $status[5] = $status[5] . ' ' . implode(' ', array_slice($status, 6));

            return array_slice($status, 0, 6);
        }, $output);

        $keys = ['user', 'pid', 'rss', 'stat', 'start', 'command'];

        foreach ($output as $key => $value) {
            $output[$key] = array_combine($keys, $value);
        }

        unset($keys);

        return $output;
    }

    /**
     * @return void
     */
    public function start()
    {
        if ($this->isRunning()) {
            Output::output(sprintf('%s:%s address already in use', $this->server->getHost(), $this->server->getPort()));
        } else {
            try {
                $this->bootstrap();
                $this->getSwoole()->start();
            } catch (\Exception $e) {
                Output::output($e->getMessage());
            }
        }
    }

    /**
     * @return int
     */
    public function shutdown()
    {
        if (false === ($status = $this->isRunning())) {
            Output::output(sprintf('Server is not running...'));
            return -1;
        }

        $pid = (int) @file_get_contents($this->getPid());

        posix_kill($pid, SIGTERM);

        Output::output(sprintf('Server [#%s] is shutdown...', $pid));

        return 0;
    }

    /**
     * @return int
     */
    public function reload()
    {
        if (false === ($status = $this->isRunning())) {
            Output::output(sprintf('Server is not running...'));
            return -1;
        }

        $pid = (int) @file_get_contents($this->getPid());

        posix_kill($pid, SIGUSR1);

        Output::output(sprintf('Server [#%s] is reloading...', $pid));

        return 0;
    }

    /**
     * @return int
     */
    public function status()
    {
        if (!($status = $this->isRunning())) {
            Output::output(sprintf('Server is not running...'));
            return -1;
        }

        $keys = array_map(function ($v) {
            return strtoupper($v);
        }, array_keys($status[0]));

        $length = 20;

        $format = function ($v) use ($length) {
            $l = floor($length - strlen($v)) / 2;
            return str_repeat(' ', $l) . $v . str_repeat(' ', (strlen($v) % 2 == 1 ? ($l) : $l + 1));
        };

        echo '|' . implode('|', array_fill(0, count($keys), str_repeat('-', $length))) . '|' . PHP_EOL;

        echo '|' . implode('|', array_map($format, $keys)) . '|' . PHP_EOL;

        echo '|' . implode('|', array_fill(0, count($keys), str_repeat('-', $length))) . '|' . PHP_EOL;
        foreach ($status as $stats) {
            echo '|' . implode('|', array_map($format, array_values($stats))) . '|' . PHP_EOL;
        }

        echo '|' . implode('|', array_fill(0, count($keys), str_repeat('-', $length))) . '|' . PHP_EOL;

        return 0;
    }

    /**
     * @param array $directories
     * @return void|int
     */
    public function watch(array $directories = ['.'])
    {
        $self = $this;

        if (false === ($status = $this->isRunning())) {
            $process = new swoole_process(function () use ($self) {
                $self->start();
            }, true);
            $process->start();
        }

        foreach ($directories as $directory) {
            Output::output(sprintf('Watching directory: ["%s"]', realpath($directory)));
        }

        $watcher = new Watcher();

        $watcher->watch($directories, function () use ($self) {
            $self->reload();
        });

        $watcher->run();

        swoole_process::wait();
    }
}