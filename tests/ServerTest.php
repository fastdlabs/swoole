<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

use Swoole\Server;
use FastD\Swoole\Server\TCP;
use PHPUnit\Framework\TestCase;


class ServerTest extends TestCase
{
    public function testNewServer()
    {
        $server = new class extends \FastD\Swoole\HTTPServer {
            /**
             * @param \FastD\Http\Request $request
             * @return \FastD\Http\Response
             */
            public function handleRequest(\FastD\Http\Request $request): \FastD\Http\Response
            {
                // TODO: Implement handleRequest() method.
            }

            /**
             * @param array $config
             * @return void
             */
            public function config(array $config): void
            {
                // TODO: Implement config() method.
            }

            /**
             * @param string $event
             * @param object $handle
             * @return \FastD\Swoole\Server\ServerInterface
             */
            public function on(string $event, object $handle): \FastD\Swoole\Server\ServerInterface
            {
                // TODO: Implement on() method.
            }

            public function close(): bool
            {
                // TODO: Implement close() method.
            }

            public function send(): bool
            {
                // TODO: Implement send() method.
            }

            public function pipeline(): bool
            {
                // TODO: Implement pipeline() method.
            }

            public function check(): bool
            {
                // TODO: Implement check() method.
            }

            public function task(): int
            {
                // TODO: Implement task() method.
            }

            public function finish(): int
            {
                // TODO: Implement finish() method.
            }

            /**
             * @param \FastD\Swoole\Handlers\HandlerInterface $handler
             * @return \FastD\Swoole\Server\ServerInterface
             */
            public function handle(\FastD\Swoole\Handlers\HandlerInterface $handler
            ): \FastD\Swoole\Server\ServerInterface {
                // TODO: Implement handle() method.
            }
        };
        $server = new TcpServerServer('foo', '127.0.0.1:9528');

        $this->assertEquals('127.0.0.1', $server->getHost());
        $this->assertEquals('9528', $server->getPort());
        $this->assertEquals('foo', $server->getName());
        $this->assertEquals('/tmp/foo.pid', $server->getPidFile());
        $this->assertNull($server->getSwoole());
    }
}
