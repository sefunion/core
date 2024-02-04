<?php


declare(strict_types=1);


namespace Easy\Generator;

use Psr\Container\ContainerInterface;

abstract class EasyGenerator
{
    public const NO = 1;

    public const YES = 2;

    protected string $stubDir;

    protected string $namespace;

    protected ContainerInterface $container;

    /**
     * EasyGenerator constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setStubDir(BASE_PATH . '/vendor/sef/core/src/Generator/Stubs/');
        $this->container = $container;
    }

    public function getStubDir(): string
    {
        return $this->stubDir;
    }

    public function setStubDir(string $stubDir)
    {
        $this->stubDir = $stubDir;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @param mixed $namespace
     */
    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    public function replace(): self
    {
        return $this;
    }
}
