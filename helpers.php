<?php

use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

function output($message)
{
    $output = new ConsoleOutput();
    $output->writeln($message);
}

/**
 * @param $name
 */
function process_rename ($name)
{
    set_error_handler(function () {
    });

    if (function_exists('cli_set_process_title')) {
        cli_set_process_title($name);
    } else if (function_exists('swoole_set_process_name')) {
        swoole_set_process_name($name);
    }

    restore_error_handler();
}

/**
 * Kill somebody
 *
 * @param $pid
 * @param int $signo
 * @return int
 */
function process_kill($pid, $signo = SIGTERM)
{
    return swoole_process::kill($pid, $signo);
}

/**
 * @param bool $blocking
 * @return array
 */
function process_wait($blocking = true)
{
    return swoole_process::wait($blocking);
}

/**
 * @param bool $nochdir
 * @param bool $noclose
 * @return mixed
 */
function process_daemon($nochdir = true, $noclose = true)
{
    return swoole_process::daemon($nochdir, $noclose);
}

/**
 * @param $signo
 * @param callable $callback
 * @return mixed
 */
function process_signal($signo, callable $callback)
{
    return swoole_process::signal($signo, $callback);
}

/**
 * @param $interval
 * @param $type
 * @return bool
 */
function process_alarm($interval, $type = ITIMER_REAL)
{
    return swoole_process::alarm($interval, $type);
}

/**
 * @param array $cpus
 * @return mixed
 */
function process_affinity(array $cpus)
{
    return swoole_process::setaffinity($cpus);
}

/**
 * @param $interval
 * @param callable $callback
 * @return mixed
 */
function timer_tick($interval, $callback, array $params = [])
{
    return swoole_timer_tick($interval, $callback, $params);
}

/**
 * @param $interval
 * @param callable $callback
 * @param array $params
 * @return mixed
 */
function timer_after($interval, $callback, array $params = [])
{
    return swoole_timer_after($interval, $callback, $params);
}

/**
 * @param $timerId
 * @return mixed
 */
function timer_clear($timerId)
{
    return swoole_timer_clear($timerId);
}

/*
 * @return string
 */
function get_local_ip()
{
    $serverIps = swoole_get_local_ip();
    $patternArray = [
        '10\.',
        '172\.1[6-9]\.',
        '172\.2[0-9]\.',
        '172\.31\.',
        '192\.168\.'
    ];

    foreach ($serverIps as $serverIp) {
        if (preg_match('#^' . implode('|', $patternArray) . '#', $serverIp)) {
            return $serverIp;
        }
    }

    return gethostbyname(gethostname());
}

/**
 * @param $keyword
 * @return bool
 */
function process_is_running($keyword)
{
    $scriptName = pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_BASENAME);

    $command = "ps axu | grep '{$keyword}' | grep -v grep | grep -v {$scriptName}";

    exec($command, $output);

    return empty($output) ? false : true;
}

/**
 * @param $port
 * @return bool
 */
function port_is_running($port)
{
    $command = "lsof -i:{$port}";

    exec($command, $output);

    return empty($output) ? false : true;
}
