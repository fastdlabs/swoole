<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/21
 * Time: 下午8:29
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Console;

use FastD\Swoole\Client;
use FastD\Swoole\Server;
use FastD\Swoole\Watch\Watcher;

/**
 * Service 管理脚本
 *
 * Class Service
 *
 * @package FastD\Swoole\Console
 */
class Service
{
    /**
     * @var static
     */
    protected static $service;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Service constructor.
     *
     * @param $server
     * @param array $config
     */
    public function __construct($server, array $config = [])
    {
        if ($server instanceof Server) {
            $this->server = $server;
            if (!empty($config)) {
                $this->server->configure($config);
            }
        } else {
            $this->server = new $server($config);
        }
    }

    /**
     * @return bool
     */
    protected function isRunning()
    {
        $processName = $this->server->getServerName();

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
                $this->server->bootstrap();
                $this->server->start();
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
            Output::output(sprintf('Server[%s] is not running...', $this->server->getServerName()));
            return -1;
        }

        $pid = (int) @file_get_contents($this->server->getPid());

        posix_kill($pid, SIGTERM);

        Output::output(sprintf('Server[%s] is shutdown...', $this->server->getServerName()));

        return 0;
    }

    /**
     * @return int
     */
    public function reload()
    {
        if (false === ($status = $this->isRunning())) {
            Output::output(sprintf('Server[%s] is not running...', $this->server->getServerName()));
            return -1;
        }

        $pid = (int)@file_get_contents($this->server->getPid());

        posix_kill($pid, SIGUSR1);

        Output::output(sprintf('Server[%s] is reloading...', $this->server->getServerName()));

        return 0;
    }

    /**
     * @return int
     */
    public function status()
    {
        if (!($status = $this->isRunning())) {
            Output::output(sprintf('Server[%s] is not running...', $this->server->getServerName()));
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
            $process = new \swoole_process(function () use ($self) {
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

        \swoole_process::wait();
    }

    /**
     * @param $server
     * @param array $config
     * @return Service
     */
    public static function server($server, array $config = [])
    {
        if (null === static::$service) {
            static::$service = new static($server, $config);
        }

        return static::$service;
    }
}