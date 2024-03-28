<?php

namespace Kindness\ModuleManage\Auth\Jwt\Facades;

use Illuminate\Support\Facades\Facade;
use Kindness\ModuleManage\Auth\Jwt\Token as BaseToken;

/**
 * @method static array generateToken(array $extend)
 * @method static array refreshToken(string $token)
 * @method static array verifyRefreshToken(string $token)
 * @method static array verifyAccessToken(string $token)
 * @method static array verify(string $token = null)
 * @method static mixed getCurrentId()
 * @method static BaseToken withConfig(array $option)
 * @method static BaseToken withEncrypted(bool $encrypted)
 * @method static BaseToken withIsNeedCache(bool $isNeedCache)
 */
class Token extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'token';
    }
}
