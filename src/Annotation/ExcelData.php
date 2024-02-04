<?php


declare(strict_types=1);


namespace Easy\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * excel导入导出元数据。
 * @Annotation
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class ExcelData extends AbstractAnnotation {}
