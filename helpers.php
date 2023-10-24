<?php

use Symfony\Component\Console\Output\ConsoleOutput;
use Swoole\Process;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

function output(string $message)
{
    $output = new ConsoleOutput();
    $date = date('Y-m-d H:i:s');
    $str = sprintf("<info>[%s]</info> %s", $date, $message);
    $str = str_replace(['[', ']'], ['<info>[', ']</info>'], $str);
    $str = str_replace(['{', '}'], ['<comment>[', ']</comment>'], $str);
    $output->writeln($str);
    unset($output);
}

function process_rename(string $name)
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

function process_kill(int $pid, int $signo = SIGTERM)
{
    return Process::kill($pid, $signo);
}

function process_wait(bool $blocking = true)
{
    return Process::wait($blocking);
}

/**
 * @param $signo
 * @param callable $callback
 * @return mixed
 */
function process_signal($signo, callable $callback)
{
    return Process::signal($signo, $callback);
}

/**
 * @param $interval
 * @param $type
 * @return bool
 */
function process_alarm($interval, $type = ITIMER_REAL)
{
    return Process::alarm($interval, $type);
}

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

function process_is_running($keyword): bool
{
    $scriptName = pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_BASENAME);

    $command = "ps axu | grep '{$keyword}' | grep -v grep | grep -v {$scriptName}";

    exec($command, $output);

    return !empty($output);
}

/**
 * @param $port
 * @return bool
 */
function port_is_running($port): bool
{
    $command = "lsof -i:{$port}";

    exec($command, $output);

    return !empty($output);
}
