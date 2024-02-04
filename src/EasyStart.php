<?php


declare(strict_types=1);


namespace Easy;

use Hyperf\Framework\Bootstrap\ServerStartCallback;
use Easy\Interfaces\ServiceInterface\ModuleServiceInterface;

class EasyStart extends ServerStartCallback
{
    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function beforeStart()
    {
        $service = container()->get(ModuleServiceInterface::class);
        $service->setModuleCache();
        $console = console();
        $console->info('EasyCMF start success...');
        $console->info($this->welcome());
        str_contains(PHP_OS, 'CYGWIN') && $console->info('current booting the user: ' . shell_exec('whoami'));
    }

    protected function welcome(): string
    {
        return sprintf('EasyCMF', date('Y'));
    }
}
