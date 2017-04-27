<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace FastD\Swoole\AsyncIO;


use SplFileObject;

/**
 * Class File
 * @package FastD\Swoole\AsyncIO
 */
class File extends SplFileObject
{
    public function __construct($file_name, $open_mode = 'wb+', $use_include_path = false, $context = null)
    {
        if (!file_exists($file_name)) {
            touch($file_name);
        }

        parent::__construct($file_name, $open_mode, $use_include_path, $context);
    }

    /**
     * @param $content
     * @param int $offset
     * @return mixed
     */
    public function write($content, $offset = -1)
    {
        return \swoole_async_write($this->getFilename(), $content, $offset, [$this, 'doWrite']);
    }

    /**
     * @param int $size
     * @param int $offset
     */
    public function read($size = 8192, $offset = 0)
    {
        \swoole_async_read($this->getFilename(), [$this, 'doRead'], $size, $offset);
    }

    /**
     * @param $filename
     * @param $content
     * @return bool
     */
    public function doRead($filename, $content)
    {
        return true;
    }

    /**
     * @param $filename
     * @param $content
     * @return bool
     */
    public function doWrite($filename, $content)
    {
        return true;
    }
}