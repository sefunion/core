<?php


declare(strict_types=1);


namespace Easy\Crontab\Mutex;

use Easy\Crontab\EasyCrontab;

interface TaskMutex
{
    /**
     * Attempt to obtain a task mutex for the given crontab.
     */
    public function create(EasyCrontab $crontab): bool;

    /**
     * Determine if a task mutex exists for the given crontab.
     */
    public function exists(EasyCrontab $crontab): bool;

    /**
     * Clear the task mutex for the given crontab.
     */
    public function remove(EasyCrontab $crontab);
}
