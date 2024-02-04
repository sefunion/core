<?php


declare(strict_types=1);


namespace Easy\Abstracts;

use Hyperf\Context\Context;
use Easy\Traits\ServiceTrait;

abstract class AbstractService
{
    use ServiceTrait;

    public $mapper;

    /**
     * 魔术方法，从类属性里获取数据.
     * @return mixed|string
     */
    public function __get(string $name)
    {
        return $this->getAttributes()[$name] ?? '';
    }

    /**
     * 把数据设置为类属性.
     */
    public function setAttributes(array $data)
    {
        Context::set('attributes', $data);
    }

    /**
     * 获取数据.
     */
    public function getAttributes(): array
    {
        return Context::get('attributes', []);
    }
}
