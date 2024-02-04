<?php

declare(strict_types=1);


namespace Easy\Listener;

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BeforeMainServerStart;
use Easy\Helper\Ip2Region;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Ip2RegionListener implements ListenerInterface
{
    public function __construct(protected ContainerInterface $container) {}

    public function listen(): array
    {
        return [
            BeforeMainServerStart::class,
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public function process(object $event): void
    {
        $this->container->get(Ip2Region::class);
    }
}
