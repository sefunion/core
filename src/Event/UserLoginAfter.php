<?php


declare(strict_types=1);


namespace Easy\Event;

class UserLoginAfter
{
    public array $userinfo;

    public bool $loginStatus = true;

    public string $message;

    public string $token;

    public function __construct(array $userinfo)
    {
        $this->userinfo = $userinfo;
    }
}
