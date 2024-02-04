<?php


declare(strict_types=1);


namespace Easy\Abstracts;

use Hyperf\Config\Annotation\Value;

/**
 * Class AbstractRedis.
 */
abstract class AbstractRedis
{
    /**
     * 缓存前缀
     */
    #[Value('cache.default.prefix')]
    protected string $prefix;

    /**
     * key 类型名.
     */
    protected string $typeName;

    /**
     * 获取实例.
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function getInstance()
    {
        return container()->get(static::class);
    }

    /**
     * 获取redis实例.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function redis(): \Hyperf\Redis\Redis
    {
        return redis();
    }

    /**
     * 获取key.
     */
    public function getKey(string $key): ?string
    {
        return empty($key) ? null : ($this->prefix . trim($this->typeName, ':') . ':' . $key);
    }
}
