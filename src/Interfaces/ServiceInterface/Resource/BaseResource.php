<?php

declare(strict_types=1);


namespace Easy\Interfaces\ServiceInterface\Resource;

/**
 * 基础资源Service.
 */
interface BaseResource
{
    public function resource(array $params = [], array $extras = []): array;
}
