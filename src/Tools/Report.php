<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Tools;

use swoole_server;
use FastD\Swoole\Async\AsyncClient;

trait Report
{
    /**
     * @param swoole_server $server
     * @param $worker_id
     * @param $task_id
     * @param $msg
     */
    public function report(swoole_server $server, $worker_id, $task_id, $msg)
    {
        foreach ($this->monitors as $monitor) {
            $client = new AsyncClient($monitor['sock']);
            if ($client->connect($monitor['host'], $monitor['port'], 2)) {
                $client->send(Binary::encode([
                    'worker_id' => $worker_id,
                    'task_id' => $task_id,
                    'msg' => $msg
                ]));
            }
            unset($client);
        }
    }
}