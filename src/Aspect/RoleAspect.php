<?php


declare(strict_types=1);


namespace Easy\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Easy\Annotation\Role;
use Easy\Exception\NoPermissionException;
use Easy\Helper\LoginUser;
use Easy\Interfaces\ServiceInterface\UserServiceInterface;
use Easy\EasyRequest;

/**
 * Class RoleAspect.
 */
#[Aspect]
class RoleAspect extends AbstractAspect
{
    public array $annotations = [
        Role::class,
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
     * RoleAspect constructor.
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
        // 是超管角色放行
        if ($this->loginUser->isAdminRole()) {
            return $proceedingJoinPoint->process();
        }

        /* @var Role $role */
        if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Role::class])) {
            $role = $proceedingJoinPoint->getAnnotationMetadata()->method[Role::class];
        }

        // 没有使用注解，则放行
        if (empty($role->code)) {
            return $proceedingJoinPoint->process();
        }

        $this->checkRole($role->code, $role->where);

        return $proceedingJoinPoint->process();
    }

    /**
     * 检查角色.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function checkRole(string $codeString, string $where): bool
    {
        $roles = $this->service->getInfo()['roles'];

        if ($where === 'OR') {
            foreach (explode(',', $codeString) as $code) {
                if (in_array(trim($code), $roles)) {
                    return true;
                }
            }
            throw new NoPermissionException(
                t('system.no_role') . ' -> [ ' . $codeString . ' ]'
            );
        }

        if ($where === 'AND') {
            foreach (explode(',', $codeString) as $code) {
                $code = trim($code);
                if (! in_array($code, $roles)) {
                    $service = container()->get(\Easy\Interfaces\ServiceInterface\RoleServiceInterface::class);
                    throw new NoPermissionException(
                        t('system.no_role') . ' -> [ ' . $service->findNameByCode($code) . ' ]'
                    );
                }
            }
        }

        return true;
    }
}
