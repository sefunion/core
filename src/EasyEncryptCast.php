<?php

namespace Easy;

use Hyperf\Contract\CastsAttributes;

class EasyEncryptCast implements CastsAttributes
{

    private string $encryptionKey;

    public function __construct()
    {
        $this->encryptionKey = config(sprintf('custom.encryption.%s.encryption_key', 'mysql'), 'default') ?? "4vYtBWNH9g52VXLSuIszixbAdOqjTm36GM1yRkUw8DE7CpfJQ0lcKaheoPZFnrJIN";
    }

    
    /**
     * 将取出的数据进行转换
     */
    public function get($model, $key ,$value, $attributes)
    {
        if (empty($value)) {
            return $value;
        }
        return decrypt($value,$this->encryptionKey);
    }

    /**
     * 转换成将要进行存储的值
     */
    public function set($model, $key ,$value, $attributes,): bool|array|string|null
    {
        if (empty($value)) {
            return $value;
        }
        return encrypt($value,$this->encryptionKey);
    }
}
