<?php

namespace FastD\Swoole\Coroutine;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

/**
 * Class MySQL
 * @package FastD\Swoole\Coroutine
 */
class MySQL
{
    /**
     * @var Swoole\Coroutine\MySQL
     */
    protected $client;

    /**
     * MySQL constructor.
     * @param $dsn
     * @param $user
     * @param $pass
     * @param $database
     */
    public function __construct($dsn, $user, $pass, $database)
    {
        $info = parse_url($dsn);
        $this->client = new Swoole\Coroutine\MySQL();
        $this->client->connect([
            'host' => $info['host'],
            'port' => $info['port'] ?? '3306',
            'user' => $user,
            'password' => $pass,
            'database' => $database
        ]);
    }

    /**
     * @param $sql
     * @param int $timeout
     * @return mixed
     */
    public function query($sql, $timeout = 0)
    {
        return $this->client->query($sql. $timeout);
    }
}