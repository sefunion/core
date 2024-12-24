<?php

namespace Easy;

use Hyperf\Contract\CastsAttributes;

class EasyArrayCast implements CastsAttributes
{
    /**
     * 将取出的数据进行转换
     */
    public function get($model, $key, $value, $attributes)
    {
        return json_decode($value ?? "", true);
    }

    /**
     * 转换成将要进行存储的值
     */
    public function set($model, $key, $value, $attributes): bool|array|string|null
    {
        if (empty($value)) {
            return null;
        }
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
