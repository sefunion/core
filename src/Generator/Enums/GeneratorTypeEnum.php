<?php

declare(strict_types=1);

namespace Easy\Generator\Enums;

/**
 * 代码生成类型.
 */
enum GeneratorTypeEnum: string
{
    /**
     * 单表.
     */
    case SINGLE = 'single';

    /**
     * 树表.
     */
    case TREE = 'tree';

    /**
     * 子父表.
     */
    case PARENT_SUB = 'parent_sub';
}