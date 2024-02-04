<?php


declare(strict_types=1);


namespace Easy\Command\Creater;

use Hyperf\Command\Annotation\Command;
use Hyperf\Support\Filesystem\FileNotFoundException;
use Hyperf\Support\Filesystem\Filesystem;
use Easy\EasyCommand;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class CreateFormRequest.
 */
#[Command]
class CreateFormRequest extends EasyCommand
{
    protected ?string $name = 'easy:request-gen';

    protected string $module;

    public function configure()
    {
        parent::configure();
        $this->setHelp('run "php bin/hyperf.php easy:module <module_name> <name>"');
        $this->setDescription('Generate validate form request class file');
        $this->addArgument(
            'module_name',
            InputArgument::REQUIRED,
            'input module name'
        );

        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'input FormRequest class file name'
        );
    }

    public function handle()
    {
        $this->module = ucfirst(trim($this->input->getArgument('module_name')));
        $this->name = ucfirst(trim($this->input->getArgument('name')));

        $fs = new Filesystem();

        try {
            $content = str_replace(
                ['{MODULE_NAME}', '{CLASS_NAME}'],
                [$this->module, $this->name],
                $fs->get($this->getStub('form_request'))
            );
        } catch (FileNotFoundException $e) {
            $this->error($e->getMessage());
            exit;
        }

        $fs->put($this->getModulePath() . $this->name . 'FormRequest.php', $content);

        $this->info('<info>[INFO] Created request:</info> ' . $this->name . 'FormRequest.php');
    }
}
