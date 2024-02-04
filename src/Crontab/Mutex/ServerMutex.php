<?php


declare(strict_types=1);


namespace Easy\Crontab\Mutex;

use Easy\Crontab\EasyCrontab;

interface ServerMutex
{
    /**
     * Attempt to obtain a server mutex for the given crontab.
     */
    public function attempt(EasyCrontab $crontab): bool;

    /**
     * Get the server mutex for the given crontab.
     */
    public function get(EasyCrontab $crontab): string;
}
