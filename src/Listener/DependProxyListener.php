<?php

declare(strict_types=1);


namespace Easy\Listener;

use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Easy\Annotation\DependProxyCollector;
use Easy\Factory\DependProxyFactory;

// #[Listener]
class DependProxyListener implements ListenerInterface
{
    public function listen(): array
    {
        return [BootApplication::class];
    }

    public function process(object $event): void
    {
        foreach (DependProxyCollector::list() as $collector) {
            $targets = $collector->values;
            $definition = $collector->provider;
            foreach ($targets as $target) {
                DependProxyFactory::define($target, $definition, true);
            }
        }
    }
}
