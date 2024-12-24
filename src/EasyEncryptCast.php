<?php

namespace Easy;

use Hyperf\Contract\CastsAttributes;

class EasyEncryptCast implements CastsAttributes
{
    /**
     * 将取出的数据进行转换
     */
    public function get($model, $key ,$value, $attributes)
    {
        if (empty($value)) {
            return $value;
        }
        $encryptionKey = config(sprintf('custom.encryption.%s.encryption_key', 'mysql'), 'default');
        // $encryptionKey = substr($encryptionKey, 4, 7);
        return decrypt($value,$encryptionKey);
    }

    /**
     * 转换成将要进行存储的值
     */
    public function set($model, $key ,$value, $attributes,): bool|array|string|null
    {
        if (empty($value)) {
            return $value;
        }
        $encryptionKey = config(sprintf('custom.encryption.%s.encryption_key', 'mysql'), 'default');
        // $encryptionKey = substr($encryptionKey, 4, 7);
        return encrypt($value,$encryptionKey);
    }
}
