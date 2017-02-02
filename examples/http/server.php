<?php
use FastD\Http\JsonResponse;
use FastD\Http\ServerRequest;

include __DIR__ . '/../../vendor/autoload.php';

class Http extends \FastD\Swoole\Server\Http
{
    /**
     * @param ServerRequest $serverRequest
     * @return JsonResponse
     */
    public function doRequest(ServerRequest $serverRequest)
    {
        return new JsonResponse([
            'msg' => 'hello world',
        ]);
    }
}

return Http::createServer('http', 'http://0.0.0.0:9527');
