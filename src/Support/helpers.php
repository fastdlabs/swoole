<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

use FastD\Swoole\Exceptions\AddressIllegalException;
use FastD\Swoole\Exceptions\CantSupportSchemeException;
use FastD\Swoole\Server;
use FastD\Swoole\Support\Output;

/**
 * @param $address
 * @return mixed
 */
function parse_address($address)
{
    if (false === ($info = parse_url($address))) {
        throw new AddressIllegalException($address);
    }

    switch (strtolower($info['scheme'])) {
        case 'tcp':
        case 'unix':
            $sock = SWOOLE_SOCK_TCP;
            break;
        case 'udp':
            $sock = SWOOLE_SOCK_UDP;
            break;
        case 'http':
        case 'ws':
        default:
            $sock = null;
    }

    $info['sock'] = $sock;

    return $info;
}

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
function timer_tick($interval, callable $callback)
{
    return swoole_timer_tick($interval, $callback);
}

/**
 * @param $timerId
 * @return mixed
 */
function timer_clear($timerId)
{
    return swoole_timer_clear($timerId);
}

/**
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

    return 'unknown';
}

/**
 * @param $name
 * @return array|bool
 */
function check_process($name)
{
    $scriptName = pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_BASENAME);

    $command = "ps axu | grep '{$name}' | grep -v grep | grep -v {$scriptName}";

    exec($command, $output);

    if (empty($output)) {
        return false;
    }

    return true;

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
