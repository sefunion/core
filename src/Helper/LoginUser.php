<?php


declare(strict_types=1);


namespace Easy\Helper;

use Easy\Exception\TokenException;
use Easy\Interfaces\ServiceInterface\RoleServiceInterface;
use Easy\Interfaces\ServiceInterface\UserServiceInterface;
use Easy\EasyRequest;
use Psr\SimpleCache\InvalidArgumentException;
use Easy\JWTAuth\JWT;

class LoginUser
{
    protected JWT $jwt;

    protected EasyRequest $request;

    /**
     * LoginUser constructor.
     * @param string $scene 场景，默认为default
     */
    public function __construct(string $scene = 'default')
    {
        /* @var JWT $this->jwt */
        $this->jwt = make(JWT::class)->setScene($scene);
    }

    /**
     * 验证token.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function check(?string $token = null, ?string $scene = null): bool
    {
        try {
            if ($this->jwt->checkToken($token, $scene, true, true, true)) {
                return true;
            }
        } catch (InvalidArgumentException $e) {
            throw new TokenException(t('jwt.no_token'));
        } catch (\Throwable $e) {
            throw new TokenException(t('jwt.no_login'));
        }

        return false;
    }

    /**
     * 获取JWT对象
     */
    public function getJwt(): Jwt
    {
        return $this->jwt;
    }

    /**
     * 获取当前登录用户信息.
     */
    public function getUserInfo(?string $token = null): array
    {
        return $this->jwt->getParserData($token);
    }

    /**
     * 获取当前登录用户ID.
     */
    public function getId(): int
    {
        return $this->jwt->getParserData()['id'];
    }

    /**
     * 获取当前登录用户名.
     */
    public function getUsername(): string
    {
        return $this->jwt->getParserData()['username'];
    }

    /**
     * 获取当前登录用户角色.
     */
    public function getUserRole(array $columns = ['id', 'name', 'code']): array
    {
        return container()->get(UserServiceInterface::class)->read($this->getId(), ['id'])->roles()->get($columns)->toArray();
    }

    /**
     * 获取当前登录用户岗位.
     */
    public function getUserPost(array $columns = ['id', 'name', 'code']): array
    {
        return container()->get(UserServiceInterface::class)->read($this->getId(), ['id'])->posts()->get($columns)->toArray();
    }

    /**
     * 获取当前登录用户部门.
     */
    public function getUserDept(array $columns = ['id', 'name']): array
    {
        return container()->get(UserServiceInterface::class)->read($this->getId(), ['id'])->depts()->get($columns)->toArray();
    }

    /**
     * 获取当前token用户类型.
     */
    public function getUserType(): string
    {
        return $this->jwt->getParserData()['user_type'];
    }

    /**
     * 是否为超级管理员（创始人），用户禁用对创始人没用.
     */
    public function isSuperAdmin(): bool
    {
        return env('SUPER_ADMIN') == $this->getId();
    }

    /**
     * 是否为管理员角色.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function isAdminRole(): bool
    {
        return in_array(
            container()->get(RoleServiceInterface::class)->read((int) env('ADMIN_ROLE'), ['code'])->code,
            container()->get(UserServiceInterface::class)->getInfo()['roles']
        );
    }

    /**
     * 获取Token.
     * @throws InvalidArgumentException
     */
    public function getToken(array $user): string
    {
        return $this->jwt->getToken($user);
    }

    /**
     * 刷新token.
     * @throws InvalidArgumentException
     */
    public function refresh(?string $token = null): string
    {
        return $this->jwt->refreshToken($token);
    }
}
