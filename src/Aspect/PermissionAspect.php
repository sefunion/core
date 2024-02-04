<?php


declare(strict_types=1);


namespace Easy\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Easy\Annotation\Permission;
use Easy\Exception\NoPermissionException;
use Easy\Helper\LoginUser;
use Easy\Interfaces\ServiceInterface\UserServiceInterface;
use Easy\EasyRequest;

/**
 * Class PermissionAspect.
 */
#[Aspect]
class PermissionAspect extends AbstractAspect
{
    public array $annotations = [
        Permission::class,
    ];

    /**
     * UserServiceInterface.
     */
    protected UserServiceInterface $service;

    /**
     * EasyRequest.
     */
    protected EasyRequest $request;

    /**
     * LoginUser.
     */
    protected LoginUser $loginUser;

    /**
     * PermissionAspect constructor.
     */
    public function __construct(
        UserServiceInterface $service,
        EasyRequest $request,
        LoginUser $loginUser
    ) {
        $this->service = $service;
        $this->request = $request;
        $this->loginUser = $loginUser;
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        if ($this->loginUser->isSuperAdmin()) {
            return $proceedingJoinPoint->process();
        }

        /* @var Permission $permission */
        if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Permission::class])) {
            $permission = $proceedingJoinPoint->getAnnotationMetadata()->method[Permission::class];
        }

        // 注解权限为空，则放行
        if (empty($permission->code)) {
            return $proceedingJoinPoint->process();
        }

        $this->checkPermission($permission->code, $permission->where);

        return $proceedingJoinPoint->process();
    }

    /**
     * 检查权限.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function checkPermission(string $codeString, string $where): bool
    {
        $codes = $this->service->getInfo()['codes'];

        if ($where === 'OR') {
            foreach (explode(',', $codeString) as $code) {
                if (in_array(trim($code), $codes)) {
                    return true;
                }
            }
            throw new NoPermissionException(
                t('system.no_permission') . ' -> [ ' . $this->request->getPathInfo() . ' ]'
            );
        }

        if ($where === 'AND') {
            foreach (explode(',', $codeString) as $code) {
                $code = trim($code);
                if (! in_array($code, $codes)) {
                    $service = container()->get(\Easy\Interfaces\ServiceInterface\MenuServiceInterface::class);
                    throw new NoPermissionException(
                        t('system.no_permission') . ' -> [ ' . $service->findNameByCode($code) . ' ]'
                    );
                }
            }
        }

        return true;
    }
}
