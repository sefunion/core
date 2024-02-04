<?php


declare(strict_types=1);


namespace Easy;

use Hyperf\Di\Annotation\Inject;
use Easy\Traits\ControllerTrait;

/**
 * 后台控制器基类
 * class EasyController.
 */
abstract class EasyController
{
    use ControllerTrait;

    #[Inject]
    protected Easy $easy;
}
