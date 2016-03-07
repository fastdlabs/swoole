<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/3/7
 * Time: 下午5:25
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Protocol;

class Packet implements ProtocolInterface
{
    const PACKET_JSON = 1;
    const PACKET_SERIALIZE = 2;
    const PACKET_DEFAULT = 3;

    protected $data;

    protected $type;

    protected $encode = false;

    protected function __construct($data, $type = Packet::PACKET_JSON, $encode = false)
    {
        $this->data = $data;

        $this->type = $type;

        $this->encode = $encode;
    }

    /**
     * 编码数据包
     *
     * @return string
     */
    public function encode()
    {
        $this->encode = true;

        switch ($this->type) {
            case static::PACKET_JSON:
                $data = json_encode($this->data, JSON_UNESCAPED_UNICODE);
                break;
            case static::PACKET_SERIALIZE:
                $data = serialize($this->data);
                break;
            case static::PACKET_DEFAULT:
            default:
                $data = $this->data;
        }

        return $data;
    }

    /**
     * 解包
     *
     * @return null|string|array
     */
    public function decode()
    {
        $this->encode = false;

        switch ($this->type) {
            case static::PACKET_JSON:
                $data = json_decode($this->data, true);
                break;
            case static::PACKET_SERIALIZE:
                $data = unserialize($this->data);
                break;
            case static::PACKET_DEFAULT:
            default:
                $data = $this->data;
        }

        return $data;
    }

    public function toJson()
    {
        if (!$this->isEncode()) {
            return $this->encode();
        }

        return $this->data;
    }

    public function toArray()
    {
        if ($this->isEncode()) {
            return $this->decode();
        }

        return $this->data;
    }

    public function toSerialize()
    {
        if (!$this->isEncode()) {
            return $this->encode();
        }

        return $this->data;
    }

    public function toRaw()
    {
        return $this->data;
    }

    public function isEncode()
    {
        return $this->encode;
    }

    public static function packet($data, $type = Packet::PACKET_JSON, $encode = false)
    {
        return new self($data, $type, $encode);
    }
}