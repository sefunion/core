<?php

declare(strict_types=1);


namespace Easy\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Resource extends AbstractAnnotation
{
    public function __construct(public string $tag) {}

    public function collectClass(string $className): void
    {
        ResourceCollector::collectClass($className, $this->tag);
    }
}
