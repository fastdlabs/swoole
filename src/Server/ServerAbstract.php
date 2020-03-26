<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Swoole;


use FastD\Swoole\Manager\Manger;
use FastD\Swoole\Server\ServerInterface;
use swoole_process;
use Swoole\Server;
use Server\Port;
use FastD\Swoole\Handlers\ServerHandlerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class Server
 * @package FastD\Swoole
 */
abstract class ServerAbstract implements ServerInterface
{
    use Manger;

    protected $protocol = 'tcp';

    /**
     * @var $name
     */
    protected $name = '';

    /**
     * @var Server
     */
    protected $swoole;

    /**
     * Swoole server run configuration.
     *
     * @var array
     */
    protected $config = [
        'worker_num' => 1,
        'task_tmpdir' => '/tmp',
        'open_cpu_affinity' => true,
    ];

    /**
     * @var string
     */
    protected $host = '127.0.0.1';

    /**
     * @var string
     */
    protected $port = '9527';

    /**
     * @var string
     */
    protected $pid_file = '';

    /**
     * @var int
     */
    protected $pid = 0;

    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * 多端口支持
     *
     * @var ServerAbstract[]
     */
    protected $listens = [];

    /**
     * @var Process[]
     */
    protected $processes = [];

    /**
     * @var Timer[]
     */
    protected $timers = [];

    /**
     * @var int
     */
    protected $fd;

    /**
     * @var ConsoleOutput
     */
    protected $output;

    /**
     * Server constructor.
     * @param string $address
     * @param array $config
     */
    public function __construct(string $address = null, array $config = [])
    {
        if (null !== $address) {
            $info = parse_url($address);

            $this->host = $info['host'];
            $this->port = $info['port'];
        }

        $this->configure($config);

        $this->output = new ConsoleOutput();
    }

    /**
     * @param string $name
     * @return ServerAbstract
     */
    public function rename(string $name): ServerInterface
    {
        $this->name = $name;

        return $this;
    }


    /**
     * @param array $config
     * @return ServerAbstract
     */
    public function configure(array $config): ServerInterface
    {
        $this->config = array_merge($this->config, $config);

        isset($config['name']) && $this->rename($config['name']);

        isset($this->config['pid_file']) && $this->pid_file = $this->config['pid_file'];

        if (empty($this->pidFile)) {
            $this->pid_file = '/tmp/' . str_replace(' ', '-', $this->name) . '.pid';
            $this->config['pid_file'] = $this->pid_file;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }

    /**
     * 守護進程
     *
     * @return ServerAbstract
     */
    public function daemon(): ServerInterface
    {
        $this->config['daemonize'] = true;

        return $this;
    }

    /**
     * @return string
     */
    public function getSocketType(): string
    {
        switch ($this->protocol) {
            case 'udp':
                $type = SWOOLE_SOCK_UDP;
                break;
            case 'unix':
                $type = SWOOLE_UNIX_STREAM;
                break;
            case 'tcp':
            default :
                $type = SWOOLE_SOCK_TCP;
        }

        return $type;
    }

    /**
     * @return Server
     */
    public function getSwoole(): Server
    {
        return $this->swoole;
    }

    /**
     * @return ServerAbstract[]
     */
    public function getListeners(): array
    {
        return $this->listens;
    }

    /**
     * @param $name
     * @return ServerAbstract
     */
    public function getListener(string $name): ServerAbstract
    {
        return $this->listens[$name];
    }

    /**
     * @return ServerAbstract
     */
    protected function handleCallback(): ServerAbstract
    {
        $handles = get_class_methods($this);
        $isListenerPort = false;
        $serverClass = get_class($this->getSwoole());
        if ('Swoole\Server\Port' == $serverClass || 'Server_port' == $serverClass) {
            $isListenerPort = true;
        }
        foreach ($handles as $value) {
            if ('on' == substr($value, 0, 2)) {
                if ($isListenerPort) {
                    if ('udp' === $this->getProtocol()) {
                        $callbacks = ['onPacket',];
                    } else {
                        $callbacks = ['onConnect', 'onClose', 'onReceive',];
                    }
                    if (in_array($value, $callbacks)) {
                        $this->swoole->on(lcfirst(substr($value, 2)), [$this, $value]);
                    }
                } else {
                    $this->swoole->on(lcfirst(substr($value, 2)), [$this, $value]);
                }
            }
        }
        return $this;
    }

    /**
     * 如果需要自定义自己的swoole服务器,重写此方法
     *
     * @return Server
     */
    public function initSwoole(): Server
    {
        return new Server($this->host, $this->port, SWOOLE_PROCESS, $this->getSocketType());
    }

    /**
     * @param ServerAbstract $server
     * @return ServerAbstract
     */
    public function listen(ServerAbstract $server): ServerAbstract
    {
        $this->listens[$server->getName()] = $server;

        return $this;
    }

    /**
     * @param Process $process
     * @return ServerAbstract
     */
    public function process(Process $process): ServerAbstract
    {
        $process->withServer($this);

        $this->processes[] = $process;

        return $this;
    }

    /**
     * @param $address
     * @param $config
     * @return ServerAbstract
     */
    public static function createServer(
        ?string $address = null,
        array $config = []): ServerAbstract
    {
        return new static($address, $config);
    }
}