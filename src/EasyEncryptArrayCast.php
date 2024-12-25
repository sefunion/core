<?php

namespace Easy;

use Hyperf\Contract\CastsAttributes;

class EasyEncryptArrayCast implements CastsAttributes
{
    private string $encryptionKey;

    public function __construct()
    {
        $this->encryptionKey = config(sprintf('custom.encryption.%s.encryption_key', 'mysql'), 'default') ?? "4vYtBWNH9g52VXLSuIszixbAdOqjTm36GM1yRkUw8DE7CpfJQ0lcKaheoPZFnrJIN";
        // $this->encryptionKey = substr($this->encryptionKey, 4, 16);
    }

    /**
     * 将取出的数据进行转换
     */
    public function get($model, $key, $value, $attributes)
    {
        if (empty($value)) {
            return $value;
        }
        return json_decode(decrypt($value,$this->encryptionKey), true);
    }

    /**
     * 转换成将要进行存储的值
     */
    public function set($model,$key, $value, $attributes): bool|array|string|null
    {
        if (empty($value)) {
            return null;
        }
        return encrypt(json_encode($value, JSON_UNESCAPED_UNICODE),$this->encryptionKey);
    }
}
