<?php

namespace Kindness\ModuleManage\Auth\Jwt;

use Kindness\ModuleManage\Auth\Jwt\Exception\jwtDecryptException;

/**
 * 加密类
 */
class Crypt
{
    /**
     * $var string
     */
    protected string $mode = 'AES-256-CBC';

    /**
     * $var string
     */
    protected string $iv = 'a91ebd0f3c65209y';

    /**
     * 设置mode
     *
     * @param string $mode
     * @return $this
     */
    public function withMode(string $mode): object
    {
        if ($mode) {
            $this->mode = $mode;
        }
        return $this;
    }

    /**
     * 设置iv
     *
     * @param string $iv
     * @return $this
     */
    public function withIv(string $iv): object
    {
        if ($iv) {
            $this->iv = $iv;
        }
        return $this;
    }

    /**
     * 加密函数
     * @param $plaintext
     * @param string $key 密钥
     * @return string 返回加密结果
     */
    public function encrypt($plaintext, $key = ''): string
    {
        $encryptContent = openssl_encrypt(
            $plaintext,
            $this->mode,
            $key,
            0,
            $this->iv);
        return base64_encode($encryptContent);
    }

    /**
     * 解密函数
     * @param $plaintext
     * @param string $key 密匙
     * @param int $ttl 过期时间
     * @return string|null 字符串类型的返回结果
     */
    public function decrypt($plaintext, $key = ''): ?string
    {

        try {
            return openssl_decrypt(
                base64_decode($plaintext),//要加/解密的内容
                $this->mode,
                $key,
                0,
                $this->iv);
        } catch (\Exception $e) {
            throw new jwtDecryptException('Token 格式错误');
        }
    }
}
