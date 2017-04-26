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