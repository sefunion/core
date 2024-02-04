<?php



declare(strict_types=1);


namespace Easy\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Database\Migrations\Migrator;
use Hyperf\Database\Seeders\Seed;
use Easy\Easy;
use Easy\EasyCommand;

/**
 * Class UpdateProjectCommand.
 */
#[Command]
class UpdateProjectCommand extends EasyCommand
{
    /**
     * 更新项目命令.
     */
    protected ?string $name = 'easy:update';

    protected array $database = [];

    protected Seed $seed;

    protected Migrator $migrator;

    /**
     * UpdateProjectCommand constructor.
     */
    public function __construct(Migrator $migrator, Seed $seed)
    {
        parent::__construct();
        $this->migrator = $migrator;
        $this->seed = $seed;
    }

    public function configure()
    {
        parent::configure();
        $this->setHelp('run "php bin/hyperf.php easy:update" Update EasyCMF system');
        $this->setDescription('EasyCMF system update command');
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        $modules = make(Easy::class)->getModuleInfo();
        $basePath = BASE_PATH . '/app/';
        $this->migrator->setConnection('default');

        foreach ($modules as $name => $module) {
            $seedPath = $basePath . $name . '/Database/Seeders/Update';
            $migratePath = $basePath . $name . '/Database/Migrations/Update';

            if (is_dir($migratePath)) {
                $this->migrator->run([$migratePath]);
            }

            if (is_dir($seedPath)) {
                $this->seed->run([$seedPath]);
            }
        }

        redis()->flushDB();

        $this->line($this->getGreenText('updated successfully...'));
    }
}
