<?php


declare(strict_types=1);


namespace Easy\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 用户角色验证。
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Role extends AbstractAnnotation
{
    /**
     * @var null|string 角色代码
     * @var string 过滤条件 为 OR 时，检查有一个通过则全部通过 为 AND 时，检查有一个不通过则全不通过
     */
    public function __construct(public ?string $code = null, public string $where = 'OR') {}
}
