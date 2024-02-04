<?php


declare(strict_types=1);


namespace Easy\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 禁止重复提交.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Resubmit extends AbstractAnnotation
{
    /**
     * @var int 限制时间（秒）
     * @var null|string 提示信息
     */
    public function __construct(public int $second = 3, public ?string $message = null) {}
}
