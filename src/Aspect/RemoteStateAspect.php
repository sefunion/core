<?php


declare(strict_types=1);


namespace Easy\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Easy\Annotation\RemoteState;
use Easy\Exception\EasyException;

/**
 * Class RemoteStateAspect.
 */
#[Aspect]
class RemoteStateAspect extends AbstractAspect
{
    public array $annotations = [
        RemoteState::class,
    ];

    /**
     * @return mixed
     * @throws EasyException
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $remote = $proceedingJoinPoint->getAnnotationMetadata()->method[RemoteState::class];
        if (! $remote->state) {
            throw new EasyException('当前功能服务已禁止使用远程通用接口', 500);
        }

        return $proceedingJoinPoint->process();
    }
}
