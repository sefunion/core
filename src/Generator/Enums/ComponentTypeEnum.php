<?php

declare(strict_types=1);

namespace Easy\Generator\Enums;

/**
 * 组件类型.
 */
enum ComponentTypeEnum: string
{
    /**
     * 模态框.
     */
    case MODAL = 'modal';

    /**
     * 拖拽.
     */
    case DRAWER = 'drawer';

    /**
     * tag.
     */
    case TAG = 'tag';
}