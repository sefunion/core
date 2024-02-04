<?php


declare(strict_types=1);


namespace Easy\Generator;

interface CodeGenerator
{
    public function generator();

    public function preview();
}
