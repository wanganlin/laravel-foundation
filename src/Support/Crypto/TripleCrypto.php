<?php

declare(strict_types=1);

namespace Juling\Foundation\Support\Crypto;

class TripleCrypto
{
    private string $key;

    public function __construct(string $key) {
        $this->key = $this->getKey($key);
    }

    private function getKey($key): string
    {
        $decoded = base64_decode(trim($key));
        // 确保密钥长度为24字节
        return str_pad(substr($decoded, 0, 24), 24, "\0");
    }

    public function encrypt($data): string {
        $encrypted = openssl_encrypt(
            $data,
            'des-ede3',  // 3DES ECB模式
            $this->key,
            OPENSSL_RAW_DATA
        );
        return base64_encode($encrypted);
    }

    public function decrypt($data): string|false {
        $decoded = base64_decode($data);
        return openssl_decrypt(
            $decoded,
            'des-ede3',
            $this->key,
            OPENSSL_RAW_DATA
        );
    }
}
