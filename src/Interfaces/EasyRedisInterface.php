<?php

declare(strict_types=1);


namespace Easy\Interfaces;

interface EasyRedisInterface
{
    /**
     * 设置 key 类型名.
     */
    public function setTypeName(string $typeName): void;

    /**
     * 获取key 类型名.
     */
    public function getTypeName(): string;
}
