<?php

declare(strict_types=1);

namespace Easy\Generator\Contracts;

use Hyperf\Collection\Collection;
use Hyperf\DbConnection\Model\Model;
use Easy\Generator\Enums\ComponentTypeEnum;
use Easy\Generator\Enums\GenerateTypeEnum;
use Easy\Generator\Enums\GeneratorTypeEnum;

/**
 * 要生成的表结构.
 */
interface GeneratorTablesContract
{
    /**
     * 获取所属模块.
     */
    public function getModuleName(): string;

    /**
     * 表名.
     */
    public function getTableName(): string;

    /**
     * 生成菜单列表.
     */
    public function getGenerateMenus(): ?string;

    /**
     * 生成类型.
     */
    public function getType(): GeneratorTypeEnum;

    /**
     * 菜单名.
     */
    public function getMenuName(): string;

    /**
     * 命名空间.
     */
    public function getNamespace(): string;

    /**
     * 控制器包名称.
     */
    public function getPackageName(): ?string;

    /**
     * 生成类型.
     */
    public function getGenerateType(): GenerateTypeEnum;

    /**
     * 组件类型.
     */
    public function getComponentType(): ComponentTypeEnum;

    /**
     * other.
     */
    public function getOptions(): array;

    /**
     * 表主键.
     */
    public function getPkName(): string;

    /**
     * 获取字段列表.
     */
    public function getColumns(): Collection;

    public function handleQuery(\Closure $closure): mixed;

    public function getId(): int;

    /**
     * 获取上级id.
     */
    public function getBelongMenuId(): int;

    public function getSystemMenuFind(\Closure $closure): Model;
}