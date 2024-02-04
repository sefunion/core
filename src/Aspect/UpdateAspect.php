<?php


declare(strict_types=1);


namespace Easy\Aspect;

use Hyperf\Context\Context;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Easy\EasyModel;
use Easy\EasyRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class UpdateAspect.
 */
#[Aspect]
class UpdateAspect extends AbstractAspect
{
    public array $classes = [
        'Easy\EasyModel::update',
    ];

    /**
     * @return mixed
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $instance = $proceedingJoinPoint->getInstance();
        // 更新更改人
        if ($instance instanceof EasyModel
            && in_array('updated_by', $instance->getFillable())
            && config('easycmf.data_scope_enabled')
            && Context::has(ServerRequestInterface::class)
            && container()->get(EasyRequest::class)->getHeaderLine('authorization')
        ) {
            try {
                $instance->updated_by = user()->getId();
            } catch (\Throwable $e) {
            }
        }
        return $proceedingJoinPoint->process();
    }
}
