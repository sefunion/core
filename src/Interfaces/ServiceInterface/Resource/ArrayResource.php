<?php

declare(strict_types=1);


namespace Easy\Interfaces\ServiceInterface\Resource;

interface ArrayResource
{
    public function getData(array $params = [], array $extras = []): array;
}
