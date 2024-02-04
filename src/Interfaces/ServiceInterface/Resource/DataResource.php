<?php

declare(strict_types=1);


namespace Easy\Interfaces\ServiceInterface\Resource;

interface DataResource
{
    /**
     * 获取数据.
     */
    public function data(array $params = [], array $extras = []): array;
}
