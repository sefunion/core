<?php


declare(strict_types=1);


namespace Easy\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Easy\EasyModel;

/**
 * Class SaveAspect.
 */
#[Aspect]
class SaveAspect extends AbstractAspect
{
    public array $classes = [
        'Easy\EasyModel::save',
    ];

    /**
     * @return mixed
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Exception
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        /** @var EasyModel $instance */
        $instance = $proceedingJoinPoint->getInstance();

        if (config('easycmf.data_scope_enabled')) {
            try {
                $user = user();
                // 设置创建人
                if ($instance instanceof EasyModel
                    && in_array($instance->getDataScopeField(), $instance->getFillable())
                    && is_null($instance[$instance->getDataScopeField()])
                ) {
                    $user->check();
                    $instance[$instance->getDataScopeField()] = $user->getId();
                }

                // 设置更新人
                if ($instance instanceof EasyModel && in_array('updated_by', $instance->getFillable())) {
                    $user->check();
                    $instance->updated_by = $user->getId();
                }
            } catch (\Throwable $e) {
            }
        }
        // 生成ID
        if ($instance instanceof EasyModel
            && ! $instance->incrementing
            && $instance->getPrimaryKeyType() === 'int'
            && empty($instance->{$instance->getKeyName()})
        ) {
            $instance->setPrimaryKeyValue(snowflake_id());
        }
        return $proceedingJoinPoint->process();
    }
}
