<?php
include __DIR__ . '/../vendor/autoload.php';

class Http extends \FastD\Swoole\Server\Http
{
    /**
     * @param \FastD\Http\SwooleServerRequest $request
     * @return mixed
     */
    public function doRequest(\FastD\Http\SwooleServerRequest $request)
    {
        return new \FastD\Http\JsonResponse([
            'msg' => 'hello world',
        ], 400, [
            'NAME' => "Jan"
        ]);
    }
}

Http::run('http', 'http://0.0.0.0:9527');
