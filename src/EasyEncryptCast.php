<?php

namespace Easy;

use Hyperf\Contract\CastsAttributes;

class EasyEncryptCast implements CastsAttributes
{
    /**
     * 将取出的数据进行转换
     */
    public function get($model, $key, $value, $attributes)
    {
        if (empty($value)) {
            return $value;
        }
        return decrypt($value);
    }

    /**
     * 转换成将要进行存储的值
     */
    public function set($model, $key, $value, $attributes): bool|array|string|null
    {
        if (empty($value)) {
            return $value;
        }
        return encrypt($value);
    }
}
