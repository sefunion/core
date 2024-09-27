<?php

declare(strict_types=1);

namespace Easy\Generator\Enums;

/**
 * 执行输出生成类型.
 */
enum GenerateTypeEnum: int
{
    /**
     * 压缩包.
     */
    case ZIP = 1;

    /**
     * 生成到模块.
     */
    case OUTPUT_MODULE = 2;
}