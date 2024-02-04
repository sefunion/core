<?php


declare(strict_types=1);


namespace Easy\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 记录操作日志注解。
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class OperationLog extends AbstractAnnotation
{
    /**
     * 菜单名称.
     * @var null|string
     */
    public function __construct(public ?string $menuName = null) {}
}
