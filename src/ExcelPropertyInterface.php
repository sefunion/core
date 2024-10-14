<?php

declare(strict_types=1);

namespace Easy;

use Easy\EasyModel;
use Psr\Http\Message\ResponseInterface;

interface ExcelPropertyInterface
{
    public function import(EasyModel $model, ?\Closure $closure = null): mixed;

    public function export(string $filename, array|\Closure $closure): ResponseInterface;
}