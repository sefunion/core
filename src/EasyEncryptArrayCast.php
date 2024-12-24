<?php

namespace Easy;

use Hyperf\Contract\CastsAttributes;

class EasyEncryptArrayCast implements CastsAttributes
{
    /**
     * 将取出的数据进行转换
     */
    public function get($model, $key, $value, $attributes)
    {
        if (empty($value)) {
            return $value;
        }
        $encryptionKey = config(sprintf('custom.encryption.%s.encryption_key', 'mysql'), 'default');
        $encryptionKey = substr($encryptionKey, 4, 16);
        return json_decode(decrypt($value,$encryptionKey), true);
    }

    /**
     * 转换成将要进行存储的值
     */
    public function set($model,$key, $value, $attributes): bool|array|string|null
    {
        if (empty($value)) {
            return null;
        }
        $encryptionKey = config(sprintf('custom.encryption.%s.encryption_key', 'mysql'), 'default');
        $encryptionKey = substr($encryptionKey, 4, 16);
        return encrypt(json_encode($value, JSON_UNESCAPED_UNICODE),$encryptionKey);
    }
}
