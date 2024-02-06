<?php


declare(strict_types=1);


namespace Easy\Command\Migrate;

use Hyperf\Database\Migrations\MigrationCreator;

class EasyMigrationCreator extends MigrationCreator
{
    public function stubPath(): string
    {
        return BASE_PATH . '/vendor/sefunion/core/src/Command/Migrate/Stubs';
    }
}
