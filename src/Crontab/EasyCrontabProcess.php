<?php



declare(strict_types=1);


namespace Easy\Crontab;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Crontab\Event\CrontabDispatcherStarted;
use Hyperf\Crontab\Strategy\StrategyInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\ProcessManager;
use Psr\Container\ContainerInterface;
use Swoole\Server;

class EasyCrontabProcess extends AbstractProcess
{
    public string $name = 'EasyCMF Crontab';

    #[Inject]
    protected EasyCrontabManage $easyCrontabManage;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var EasyCrontabScheduler
     */
    private $scheduler;

    /**
     * @var StrategyInterface
     */
    private $strategy;

    /**
     * @var StdoutLoggerInterface
     */
    private $logger;

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->scheduler = $container->get(EasyCrontabScheduler::class);
        $this->strategy = $container->get(EasyCrontabStrategy::class);
        $this->logger = $container->get(StdoutLoggerInterface::class);
    }

    public function bind($server): void
    {
        $this->server = $server;
        parent::bind($server);
    }

    /**
     * 是否自启进程.
     * @param \Swoole\Coroutine\Server|\Swoole\Server $server
     */
    public function isEnable($server): bool
    {
        if (! file_exists(BASE_PATH . '/.env')) {
            return false;
        }
        return true;
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function handle(): void
    {
        $this->event->dispatch(new CrontabDispatcherStarted());
        while (ProcessManager::isRunning()) {
            $this->sleep();
            $crontabs = $this->scheduler->schedule();
            while (! $crontabs->isEmpty()) {
                /**
                 * @var EasyCrontab $crontab
                 */
                $crontab = $crontabs->dequeue();
                $this->strategy->dispatch($crontab);
            }
        }
    }

    private function sleep()
    {
        $current = date('s', time());
        $sleep = 60 - $current;
        $this->logger->debug('EasyCMF Crontab dispatcher sleep ' . $sleep . 's.');
        $sleep > 0 && \Swoole\Coroutine::sleep($sleep);
    }
}
