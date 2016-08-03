<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Http;

/**
 * Class Session
 * @package FastD\Http
 */
class HttpSession
{
    const TOKEN = 'sws';

    /**
     * @var string
     */
    protected $sessionId;

    protected $sessionFile;

    /**
     * @var mixed
     */
    protected $session;

    /**
     * @var bool
     */
    protected $started = false;

    /**
     * Session constructor.
     * @param \swoole_http_request $request
     * @param string $path
     */
    public function __construct(\swoole_http_request $request, $path = '/tmp')
    {
        $this->sessionStart($request, $path);
    }

    /**
     * @param \swoole_http_request $request
     * @param $path
     * @return bool
     */
    protected function sessionStart(\swoole_http_request $request, $path)
    {
        if (!$this->started) {
            if (isset($request->cookie[static::TOKEN])) {
                $sessionHash = $request->cookie[static::TOKEN];
                $this->sessionFile = $path . DIRECTORY_SEPARATOR . $sessionHash;
                if (file_exists($this->sessionFile)) {
                    $this->session = json_decode(file_get_contents($this->sessionFile), true);
                }
            } else {
                $this->sessionId = $this->buildSessionId();
                $this->sessionFile = $path . DIRECTORY_SEPARATOR . $this->sessionId;
            }

            $this->started = true;
        }

        return true;
    }

    /**
     * @return string
     */
    protected function buildSessionId()
    {
        return md5(password_hash(microtime(true), PASSWORD_DEFAULT));
    }

    /**
     * @param $name
     * @return bool
     */
    public function get($name)
    {
        return $this->session[$name] ?? false;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->session[$name] = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHit()
    {
        return !empty($this->session);
    }

    public function __destruct()
    {

    }
}
