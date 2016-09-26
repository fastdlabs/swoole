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
 * Class HttpSession
 *
 * @package FastD\Swoole\Http
 */
class HttpSession
{
    const TOKEN = 'sws';

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var string
     */
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
     * @param HttpRequest $request
     * @param string $path
     */
    public function __construct(HttpRequest $request, $path = '/tmp')
    {
        $this->sessionStart($request, $path);
    }

    /**
     * @param HttpRequest $request
     * @param $path
     * @return bool
     */
    protected function sessionStart(HttpRequest $request, $path)
    {
        if (!$this->started) {
            if (isset($request->cookie[static::TOKEN])) {
                $this->sessionId = $request->cookie[static::TOKEN];
                $this->sessionFile = $path . DIRECTORY_SEPARATOR . $this->sessionId;
                if (file_exists($this->sessionFile)) {
                    $this->session = json_decode(file_get_contents($this->sessionFile), true);
                } else {
                    $this->sessionId = $this->buildSessionId();
                    $this->sessionFile = $path . DIRECTORY_SEPARATOR . $this->sessionId;
                    unset($request->cookie[static::TOKEN]);
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
        return md5(microtime(true) . mt_rand(000000, 999999));
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
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
     * @return $this
     */
    public function clear()
    {
        $this->session = null;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHit()
    {
        return '' != $this->session;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return (array) $this->session;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->session);
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        if ($this->isHit()) {
            file_put_contents($this->sessionFile, $this->toJson());
        }
    }
}
