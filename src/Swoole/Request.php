<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;

/**
 * Swoole request 请求对象, 支持 tcp, udp, http swoole 内置对象封装。
 *
 * Class Request
 *
 * @package FastD\Swoole
 */
class Request
{
    /**
     * @var \swoole_server
     */
    protected $swooleServer;

    /**
     * @var int
     */
    protected $fd;

    /**
     * @var string
     */
    protected $data;

    /**
     * @var int
     */
    protected $fromId;

    /**
     * @var null
     */
    protected $clientInfo;

    /**
     * @var array
     */
    public $get;

    /**
     * @var array
     */
    public $post;

    /**
     * @var array
     */
    public $headers;

    /**
     * @var array
     */
    public $cookie;

    /**
     * @var array
     */
    public $files;

    /**
     * @var array
     */
    public $server;

    /**
     * Request constructor.
     *
     * @param \swoole_server|\swoole_http_request $server
     * @param $fd
     * @param $data
     * @param null $from_id
     * @param null $clientInfo
     */
    public function __construct($server, $fd, $data, $from_id = null, $clientInfo = null)
    {
        $this->swooleServer = $server;

        $this->fd = $fd;

        $this->data = $data;

        $this->fromId = $from_id;

        $this->clientInfo = $clientInfo;

        if ($this->server instanceof \swoole_http_request) {
            $this->parseHttpRequest($this->swooleServer);
        }
    }

    /**
     * @return string
     */
    public function getPathInfo()
    {
        return $this->server['PATH_INFO'];
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->server['REQUEST_URI'];
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->server['REQUEST_METHOD'];
    }

    /**
     * @param \swoole_http_request $request
     */
    public function parseHttpRequest(\swoole_http_request $request)
    {
        $config = [
            'document_root'     => realpath('.'),
            'script_name'       => '',
        ];

        $config['script_filename'] = str_replace('//', '/', $config['document_root'] . '/' . $config['script_name']); // Equal nginx fastcgi_params $document_root$fastcgi_script_name;

        $this->get        = isset($request->get) ? $request->get : [];
        $this->post       = isset($request->post) ? $request->post : [];
        $this->cookie    = isset($request->cookie) ? $request->cookie : [];
        $this->files      = isset($request->files) ? $request->files : [];
        $this->server     = (function (\swoole_http_request $request, $config) {
            return [
                // Server
                'REQUEST_METHOD'    => $request->server['request_method'],
                'REQUEST_URI'       => $request->server['request_uri'],
                'PATH_INFO'         => $request->server['path_info'],
                'REQUEST_TIME'      => $request->server['request_time'],
                'GATEWAY_INTERFACE' => 'fastd_swoole/' . SWOOLE_VERSION,
                // Swoole and general server proxy or server configuration.
                'SERVER_PROTOCOL'   => isset($request->header['server_protocol']) ? $request->header['server_protocol'] : $request->server['server_protocol'],
                'REQUEST_SCHEMA'    => isset($request->header['request_scheme']) ? $request->header['request_scheme'] : explode('/',$request->server['server_protocol'])[0],
                'SERVER_NAME'       => isset($request->header['server_name']) ? $request->header['server_name'] : $request->header['host'],
                'SERVER_ADDR'       => isset($request->header['server_addr']) ? $request->header['server_addr'] : $request->header['host'],
                'SERVER_PORT'       => isset($request->header['server_port']) ? $request->header['server_port'] : $request->server['server_port'],
                'REMOTE_ADDR'       => isset($request->header['remote_addr']) ? $request->header['remote_addr'] : $request->server['remote_addr'],
                'REMOTE_PORT'       => isset($request->header['remote_port']) ? $request->header['remote_port'] : $request->server['remote_port'],
                'QUERY_STRING'      => isset($request->server['query_string']) ? $request->server['query_string'] : '',
                'DOCUMENT_ROOT'     => $config['document_root'],
                'SCRIPT_FILENAME'   => $config['script_filename'],
                'SCRIPT_NAME'       => '/' . $config['script_name'],
                'PHP_SELF'          => '/' . $config['script_name'],
                'HTTP_FD'           => $request->fd,

                // Header
                'HTTP_HOST'             => $request->header['host'] ?? '::1',
                'HTTP_USER_AGENT'       => $request->header['user-agent'] ?? '',
                'HTTP_ACCEPT'           => $request->header['accept'] ?? '*/*',
                'HTTP_ACCEPT_LANGUAGE'  => $request->header['accept-language'] ?? '',
                'HTTP_ACCEPT_ENCODING'  => $request->header['accept-encoding'] ?? '',
                'HTTP_CONNECTION'       => $request->header['connection'] ?? '',
                'HTTP_CACHE_CONTROL'    => isset($request->header['cache-control']) ? $request->header['cache-control'] : '',
            ];
        })($request, $config);
    }

    /**
     * @return \swoole_server
     */
    public function getServer()
    {
        return $this->swooleServer;
    }

    /**
     * @return int
     */
    public function getFd()
    {
        return $this->fd;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int|null
     */
    public function getFromId()
    {
        return $this->fromId;
    }

    /**
     * @return null
     */
    public function getClientInfo()
    {
        return $this->clientInfo;
    }
}