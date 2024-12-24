<?php


declare(strict_types=1);


namespace Easy\Abstracts;

use Hyperf\Context\Context;
use Easy\EasyModel;
use Easy\Traits\MapperTrait;

/**
 * Class AbstractMapper.
 */
abstract class AbstractMapper
{
    use MapperTrait;

    /**
     * @var EasyModel
     */
    public $model;

    public function __construct()
    {
        $this->assignModel();
    }

    /**
     * 魔术方法，从类属性里获取数据.
     * @return mixed|string
     */
    public function __get(string $name)
    {
        return $this->getAttributes()[$name] ?? '';
    }

    abstract public function assignModel();

    /**
     * 把数据设置为类属性.
     */
    public static function setAttributes(array $data)
    {
        Context::set('attributes', $data);
    }

    /**
     * 获取数据.
     */
    public function getAttributes(): mixed
    {
        return Context::get('attributes', []);
    }
}
