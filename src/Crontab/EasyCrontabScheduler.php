<?php


declare(strict_types=1);


namespace Easy\Crontab;

class EasyCrontabScheduler
{
    /**
     * EasyCrontabManage.
     */
    protected EasyCrontabManage $crontabManager;

    /**
     * \SplQueue.
     */
    protected \SplQueue $schedules;

    /**
     * EasyCrontabScheduler constructor.
     */
    public function __construct(EasyCrontabManage $crontabManager)
    {
        $this->schedules = new \SplQueue();
        $this->crontabManager = $crontabManager;
    }

    public function schedule(): \SplQueue
    {
        foreach ($this->getSchedules() ?? [] as $schedule) {
            $this->schedules->enqueue($schedule);
        }
        return $this->schedules;
    }

    protected function getSchedules(): array
    {
        return $this->crontabManager->getCrontabList();
    }
}
