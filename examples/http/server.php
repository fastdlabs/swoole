<?php
use FastD\Http\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

include __DIR__ . '/../../vendor/autoload.php';

class Http extends \FastD\Swoole\Server\HTTP
{
    /**
     * @param ServerRequestInterface $serverRequest
     * @return JsonResponse
     */
    public function doRequest(ServerRequestInterface $serverRequest)
    {
        return new JsonResponse([
            'msg' => 'hello world',
        ]);
    }
}

return Http::createServer('http', 'http://0.0.0.0:9527');
