<?php
/**
 * Description:消费切面
 * Created by phpStorm.
 * User: mike
 * Date: 2021/11/19
 * Time: 下午2:14.
 */
declare(strict_types=1);


namespace Easy\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Easy\Amqp\Event\AfterConsume;
use Easy\Amqp\Event\BeforeConsume;
use Easy\Amqp\Event\FailToConsume;

/**
 * Class ConsumeAspect.
 */
#[Aspect]
class ConsumeAspect extends AbstractAspect
{
    public array $classes = [
        'Hyperf\Amqp\Message\ConsumerMessage::consumeMessage',
    ];

    /**
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $data = $proceedingJoinPoint->getArguments()[0];
        $message = $proceedingJoinPoint->getArguments()[1];
        try {
            event(new BeforeConsume($message, $data));
            $result = $proceedingJoinPoint->process();
            event(new AfterConsume($message, $data, $result));
            return $result;
        } catch (\Throwable $e) {
            event(new FailToConsume($message, $data, $e));
            return null;
        }
    }
}
