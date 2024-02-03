<?php

namespace Kindness\ModuleManage\Auth\Jwt;

use Kindness\ModuleManage\Auth\Jwt\Contracts\TokenFactory;


/**
 * @method static JwtToken withConfig(array $option)
 * @method static JwtToken withIsNeedCache(bool $isNeedCache)
 * @method static mixed getCurrentId()
 * @method static mixed getUser()
 * @method static string getExtendVal(string $key)
 * @method static array getExtend()
 * @method static array refreshToken($token)
 * @method static array generateToken(array $extend)
 * @method static array verify(int $tokenType, string $token)
 * @method static bool getTokenExp(int $tokenType, string $token)
 * @method static bool clear()
 */
class Token
{
    /**
     * @var TokenFactory
     */
    protected static $_instance;

    public static function instance(): TokenFactory|JwtToken
    {
        if (!static::$_instance) {
            static::$_instance = new JwtToken();
        }
        return static::$_instance;
    }

    public static function __callStatic(string $method, array $arguments)
    {
        return static::instance()->$method(...$arguments);
    }
}