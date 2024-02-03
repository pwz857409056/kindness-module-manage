<?php

namespace Kindness\ModuleManage\Auth\Jwt;

use Kindness\ModuleManage\Auth\Jwt\Exception\jwtDecryptException;
use phpseclib3\Crypt\AES;

/**
 * 加密类
 */
class Crypt
{
    /**
     * $var string
     */
    protected string $mode = 'cbc';

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
        if (empty($plaintext) || empty($key)) {
            return $plaintext;
        }

        $plaintext = bin2hex(time() . '_' . $plaintext);
        $aes = new AES($this->mode);
        $aes->setIV($this->iv);
        $aes->setKey($key);
        $encodeData = $aes->encrypt($plaintext);

        return bin2hex($encodeData);
    }

    /**
     * 解密函数
     * @param $plaintext
     * @param string $key 密匙
     * @param int $ttl 过期时间
     * @return string|null 字符串类型的返回结果
     */
    public function decrypt($plaintext, $key = '', $ttl = 0): ?string
    {

        try {
            if (empty($plaintext) || empty($key)) {
                return $plaintext;
            }
            $aes = new AES($this->mode);
            $aes->setIV($this->iv);
            $aes->setKey($key);
            $decodeData = $aes->decrypt(hex2bin($plaintext));// hex2bin = pack("H*", $hex_string)
            $decodeData = trim(hex2bin($decodeData));
            if (preg_match('/\d{10}_/s', substr($decodeData, 0, 11))) {
                if ($ttl > 0 && (time() - substr($decodeData, 0, 10) > $ttl)) {
                    $decodeData = null;
                } else {
                    $decodeData = substr($decodeData, 11);
                }
            }
            return $decodeData;
        } catch (\Exception $e) {
            throw new JwtDecryptException('字符串 格式错误');
        }
    }
}
