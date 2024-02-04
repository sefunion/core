<?php


declare(strict_types=1);


namespace Easy\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 设置某个万能通用接口状态，true 允许使用，false 禁止使用.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class RemoteState extends AbstractAnnotation
{
    /**
     * @param bool $state 状态
     */
    public function __construct(public bool $state = true) {}
}
