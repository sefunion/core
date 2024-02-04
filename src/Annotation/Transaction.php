<?php


declare(strict_types=1);


namespace Easy\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 数据库事务注解。
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Transaction extends AbstractAnnotation
{
    /**
     * @param int $retry 重试次数
     */
    public function __construct(public int $retry = 1) {}
}
