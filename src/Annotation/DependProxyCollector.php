<?php

declare(strict_types=1);


namespace Easy\Annotation;

use Hyperf\Di\MetadataCollector;

/**
 * 依赖代理收集器.
 */
class DependProxyCollector extends MetadataCollector
{
    protected static array $container = [];

    public static function setAround(string $class, $value): void
    {
        static::$container[$class] = $value;
    }

    public static function getDependencies(): array
    {
        if (empty(self::$container)) {
            return [];
        }
        $dependencies = [];
        foreach (self::$container as $collector) {
            $targets = $collector->values;
            $definition = $collector->provider;
            foreach ($targets as $target) {
                $dependencies[$target] = $definition;
            }
        }
        return $dependencies;
    }

    public static function walk(callable $closure): void
    {
        if (empty(self::$container)) {
            return;
        }
        foreach (self::$container as $collector) {
            $targets = $collector->values;
            $definition = $collector->provider;
            foreach ($targets as $target) {
                $closure($target, $definition);
            }
        }
    }
}
