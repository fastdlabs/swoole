<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @see      https://www.github.com/fastdlabs
 * @see      http://www.fastdlabs.com/
 */

include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/server.php';
include __DIR__ . '/handler.php';

$server = new Server();

$server->handle(new Handler());

$server->start();