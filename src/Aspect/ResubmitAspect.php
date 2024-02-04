<?php


declare(strict_types=1);


namespace Easy\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Easy\Annotation\Resubmit;
use Easy\Exception\EasyException;
use Easy\Exception\NormalStatusException;
use Easy\EasyRequest;
use Easy\Redis\EasyLockRedis;

/**
 * Class ResubmitAspect.
 */
#[Aspect]
class ResubmitAspect extends AbstractAspect
{
    public array $annotations = [
        Resubmit::class,
    ];

    /**
     * @return mixed
     * @throws Exception
     * @throws \Throwable
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        try {
            $result = $proceedingJoinPoint->process();

            /* @var $resubmit Resubmit */
            if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Resubmit::class])) {
                $resubmit = $proceedingJoinPoint->getAnnotationMetadata()->method[Resubmit::class];
            }

            $request = container()->get(EasyRequest::class);

            $key = md5(sprintf('%s-%s-%s', $request->ip(), $request->getPathInfo(), $request->getMethod()));

            $lockRedis = new EasyLockRedis();
            $lockRedis->setTypeName('resubmit');

            if ($lockRedis->check($key)) {
                $lockRedis = null;
                throw new NormalStatusException($resubmit->message ?: t('easycmf.resubmit'), 500);
            }

            $lockRedis->lock($key, $resubmit->second);
            $lockRedis = null;

            return $result;
        } catch (\Throwable $e) {
            throw new EasyException($e->getMessage(), $e->getCode());
        }
    }
}
