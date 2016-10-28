<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Tools;

use swoole_process;
use FastD\Swoole\Async\AsyncClient;
use FastD\Packet\Binary;

trait Discovery
{
    /**
     * 服务发现
     *
     * @param array $discoveries
     * @return $this
     */
    public function discovery(array $discoveries)
    {
        $this->discoveries = $discoveries;

        foreach ($discoveries as $discovery) {
            $process = new swoole_process(function () use ($discovery) {
                while (true) {
                    sleep(1);
                    echo 'discovery ' . $discovery['host'] . PHP_EOL;
                }
            });

            $this->swoole->addProcess($process);
        }

        return $this;
    }

    /**
     * @param array $monitors
     * @return $this
     */
    public function monitoring(array $monitors)
    {
        $this->monitors = $monitors;

        $self = $this;

        foreach ($this->monitors as $monitor) {
            $process = new swoole_process(function () use ($monitor, $self) {
                $client = new AsyncClient($monitor['sock']);
                while (true) {
                    $client->connect($monitor['host'], $monitor['port']);
                    $client->send(Binary::encode([
                        'host' => $self->getHost(),
                        'port' => $self->getPort(),
                        'status' => $self->getSwooleInstance()->stats(),
                    ]));
                    sleep(20);
                }
            });

            $this->swoole->addProcess($process);
        }

        return $this;
    }
}