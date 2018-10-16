<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole\Support;


use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

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
    protected $watchFileExt = ['php'];

    /**
     * @var array
     */
    protected $watchDir = [];

    /**
     * @var \Closure
     */
    protected $callback;

    /**
     * @var ConsoleOutput|OutputInterface
     */
    protected $output;

    /**
     * Watcher constructor.
     */
    public function __construct(OutputInterface $output = null)
    {
        if (null === $output) {
            $output = new ConsoleOutput();
        }
        $this->output = $output;

        $this->inotify = inotify_init();
    }

    /**
     * Clear all watching.
     *
     * @return void
     */
    public function clearWatch()
    {
        foreach ($this->watchDir as $wd) {
            inotify_rm_watch($this->inotify, $wd);
        }
        $this->watchDir = [];
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

            $this->watchDir[$directory] = $wd;
        }

        $this->callback = $callback;

        // Listen modify.
        \swoole_event_add($this->inotify, function () use ($callback) {
            $events = inotify_read($this->inotify);
            if (!$events) {
                return;
            }

            foreach ($events as $event) {
                if (!empty($event['name'])) {
                    $this->output->writeln(sprintf("[%s]\t" . sprintf('<info>[%s]</info> modify', $event['name']), date('Y-m-d H:i:s'))) ;
                }
            }

            if (is_callable($callback)) {
                $callback($this);
            }
        });

        return $this;
    }

    public function __destruct()
    {
        $this->clearWatch();

        fclose($this->inotify);
    }

    /**
     * @return void
     */
    public function run()
    {
        \swoole_event_wait();
    }
}