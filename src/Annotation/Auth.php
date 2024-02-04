<?php


declare(strict_types=1);


namespace Easy\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 用户登录验证。
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Auth extends AbstractAnnotation
{
    /**
     * @param string $scene 场景名
     */
    public function __construct(public string $scene = 'default') {}
}
