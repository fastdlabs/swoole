<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

include __DIR__ . '/../vendor/autoload.php';

use FastD\Swoole\Monitor\Monitor;
use FastD\Swoole\Console\Service;

$service = Service::server(Monitor::class, [
    'host' => '0.0.0.0',
    'port' => '9882',
    'ports' => [
        [
            'host' => '127.0.0.1',
            'port' => '9883',
            'sock' => SWOOLE_SOCK_TCP,
            'config' => [],
        ]
    ]
]);

$action = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'status';

switch ($action) {
    case 'status':
        $service->status();
        break;
    case 'start':
        $service->start();
        break;
    case 'stop':
        $service->shutdown();
        break;
    case 'reload':
        $service->reload();
        break;
    case 'watch':
        $service->watch(['.']);
        break;
}