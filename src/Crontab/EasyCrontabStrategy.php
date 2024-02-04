<?php


declare(strict_types=1);


namespace Easy\Crontab;

use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;

use function Hyperf\Coroutine\co;

class EasyCrontabStrategy
{
    /**
     * EasyCrontabManage.
     */
    #[Inject]
    protected EasyCrontabManage $easyCrontabManage;

    /**
     * EasyExecutor.
     */
    #[Inject]
    protected EasyExecutor $executor;

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function dispatch(EasyCrontab $crontab)
    {
        co(function () use ($crontab) {
            if ($crontab->getExecuteTime() instanceof Carbon) {
                $wait = $crontab->getExecuteTime()->getTimestamp() - time();
                $wait > 0 && \Swoole\Coroutine::sleep($wait);
                $this->executor->execute($crontab);
            }
        });
    }

    /**
     * 执行一次
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function executeOnce(EasyCrontab $crontab)
    {
        co(function () use ($crontab) {
            $this->executor->execute($crontab);
        });
    }
}
