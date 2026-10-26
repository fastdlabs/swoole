<?php

declare(strict_types=1);

use FastD\Http\Cookie;
use FastD\Http\Response\Text;
use FastD\Swoole\Listener\Server\RequestListener;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * RequestListener cookie 透传测试。
 *
 * 覆盖 FastD\Http\Cookie（相对过期 + 空值删除）到 Swoole cookie 参数
 * （绝对时间戳）的映射，避免多 Cookie 在 Swoole 下被 header 覆盖。
 */
class RequestListenerCookieTest extends TestCase
{
    private function listener(): RequestListener
    {
        return new class extends RequestListener {
            public function onRequest(ServerRequestInterface $serverRequest): ResponseInterface
            {
                return new Text();
            }

            public function expose(?ResponseInterface $response = null): array
            {
                return $this->extractCookies($response ?? new Text());
            }
        };
    }

    public function testPersistentCookieUsesAbsoluteExpiry(): void
    {
        $response = (new Text())->withCookie(
            Cookie::create('token', 'abc', 3600, '/app', 'example.com', true, true, 'Strict')
        );

        $cookies = $this->listener()->expose($response);

        $this->assertCount(1, $cookies);
        [$name, $value, $expires, $path, $domain, $secure, $httpOnly, $sameSite] = $cookies[0];

        $this->assertSame('token', $name);
        $this->assertSame('abc', $value);
        $this->assertEqualsWithDelta(time() + 3600, $expires, 2);
        $this->assertSame('/app', $path);
        $this->assertSame('example.com', $domain);
        $this->assertTrue($secure);
        $this->assertTrue($httpOnly);
        $this->assertSame('Strict', $sameSite);
    }

    public function testSessionCookieHasNoExpiry(): void
    {
        $response = (new Text())->withCookie(Cookie::create('FASTD_SESSID', 'abc'));

        $cookies = $this->listener()->expose($response);

        $this->assertSame('FASTD_SESSID', $cookies[0][0]);
        $this->assertSame(0, $cookies[0][2]);
    }

    public function testEmptyCookieValueBecomesDeletionWithPastExpiry(): void
    {
        $response = (new Text())->withCookie(Cookie::create('FASTD_SESSID', ''));

        $cookies = $this->listener()->expose($response);

        $this->assertSame('', $cookies[0][1]);
        $this->assertLessThan(time(), $cookies[0][2]);
    }

    public function testPlainPsrResponseHasNoCookies(): void
    {
        $this->assertSame([], $this->listener()->expose(new Text()));
    }
}
