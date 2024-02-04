<?php


declare(strict_types=1);


namespace Easy\Crontab\Mutex;

use Hyperf\Redis\RedisFactory;
use Easy\Crontab\EasyCrontab;

class RedisTaskMutex implements TaskMutex
{
    /**
     * @var RedisFactory
     */
    private $redisFactory;

    public function __construct(RedisFactory $redisFactory)
    {
        $this->redisFactory = $redisFactory;
    }

    /**
     * Attempt to obtain a task mutex for the given crontab.
     */
    public function create(EasyCrontab $crontab): bool
    {
        return (bool) $this->redisFactory->get($crontab->getMutexPool())->set(
            $this->getMutexName($crontab),
            $crontab->getName(),
            ['NX', 'EX' => $crontab->getMutexExpires()]
        );
    }

    /**
     * Determine if a task mutex exists for the given crontab.
     */
    public function exists(EasyCrontab $crontab): bool
    {
        return (bool) $this->redisFactory->get($crontab->getMutexPool())->exists(
            $this->getMutexName($crontab)
        );
    }

    /**
     * Clear the task mutex for the given crontab.
     */
    public function remove(EasyCrontab $crontab)
    {
        $this->redisFactory->get($crontab->getMutexPool())->del(
            $this->getMutexName($crontab)
        );
    }

    protected function getMutexName(EasyCrontab $crontab): string
    {
        return 'framework' . DIRECTORY_SEPARATOR . 'crontab-' . sha1($crontab->getName() . $crontab->getRule());
    }
}
