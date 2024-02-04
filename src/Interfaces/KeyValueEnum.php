<?php

declare(strict_types=1);


namespace Easy\Interfaces;

/**
 * key/value 枚举接口.
 */
interface KeyValueEnum
{
    public function key();

    public function value();
}
