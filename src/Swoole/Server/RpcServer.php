<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/3/7
 * Time: 下午5:55
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Server;

class RpcServer extends Server
{
    protected $process_name = 'fd-rpc';

    protected $actions = [];

    public function add($name, $action)
    {
        $this->actions[$name] = $action;

        return $this;
    }

    public function set(array $actions)
    {
        $this->actions = array_merge($this->actions, $actions);

        return $this;
    }

    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('Action ["%s"] is undefined.', $name));
        }

        return $this->actions[$name];
    }

    public function has($name)
    {
        return isset($this->actions[$name]);
    }

    public function call($name, $args)
    {
        try {
            $callback = $this->get($name);
            if (is_callable($callback)) {
                return call_user_func($callback, $args);
            }
            return call_user_func_array($callback, $args);
        } catch (\Exception $e) {
            return [
                'msg' => $e->getMessage(),
                'code' => $e->getCode()
            ];
        }
    }
}