<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/2/14
 * Time: 下午5:41
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Swoole\Manager;

/**
 * Class Watcher
 *
 * @package FastD\Swoole\Manager
 */
class Watcher
{
    /**
     * Handle events.
     *
     * @var int
     */
    protected $events = IN_MODIFY | IN_DELETE | IN_CREATE | IN_MOVE;

    /**
     * @var resource
     */
    protected $inotify;

    /**
     * @var array
     */
    protected $watch_file_ext = ['php'];

    /**
     * @var array
     */
    protected $watch_dir = [];

    /**
     * @var \Closure
     */
    protected $callback;

    /**
     * Watcher constructor.
     */
    public function __construct()
    {
        $this->inotify = inotify_init();
    }

    /**
     * Clear all watching.
     *
     * @return void
     */
    function clearWatch()
    {
        foreach ($this->watch_dir as $wd) {
            inotify_rm_watch($this->inotify, $wd);
        }
        $this->watch_dir = [];
    }

    /**
     * @param array         $directories
     * @param \Closure|null $callback
     * @return Watcher
     * @throws \RuntimeException
     */
    public function watch(array $directories, \Closure $callback = null)
    {
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                $this->clearWatch();
                throw new \RuntimeException("[$directory] is not a directory.");
            }

            $wd = inotify_add_watch($this->inotify, $directory, $this->events);

            $this->watch_dir[$directory] = $wd;
        }

        $this->callback = $callback;

        // Listen modify.
        swoole_event_add($this->inotify, function () use ($callback) {
            $events = inotify_read($this->inotify);
            if (!$events) {
                return;
            }

            foreach ($events as $event) {
                if (!empty($event['name'])) {
                    $this->output($event['name'] . ' modify');
                }
            }

            if (is_callable($callback)) {
                $callback($this);
            }

            $this->output('-------');
        });

        return $this;
    }

    /**
     * @param $msg
     * @return void
     */
    public function output($msg)
    {
        echo sprintf("[%s]\t" . $msg . '...' . PHP_EOL, date('Y-m-d H:i:s'));
    }

    /**
     * @return void
     */
    public function run()
    {
        swoole_event_wait();
    }
}