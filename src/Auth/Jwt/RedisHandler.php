<?php

namespace Kindness\ModuleManage\Auth\Jwt;

use Kindness\ModuleManage\Auth\Jwt\Exception\JwtRefreshTokenExpiredException;
use Kindness\ModuleManage\Auth\Jwt\Exception\JwtTokenExpiredException;
use support\Redis;

/**
 * Class RedisHandler
 * @package App\Library\jwt
 */
class RedisHandler
{
    /**
     * @desc: 检查设备缓存令牌
     */
    public static function verifyToken(string $pre, array $decodeToken, string $token): bool
    {
        $id = $decodeToken['extend']['id'];
        $jti = $decodeToken['jti'];
        $cacheKey = $pre . $id . ':' . $jti;
        if (!Redis::exists($cacheKey)) {
            throw new jwtTokenExpiredException('令牌已过期');
        }
        if (Redis::get($cacheKey) != $token) {
            throw new jwtRefreshTokenExpiredException('该账号已在其他设备登录，强制下线');
        }
        return true;
    }

    /**
     * @param string $pre
     * @param array $decodeToken
     * @return bool
     */
    public static function verifyRefreshToken(string $pre, array $decodeToken): bool
    {
        $id = $decodeToken['extend']['id'];
        $jti = $decodeToken['jti'];
        $cacheKey = $pre . $id . ':' . $jti;
        $token = Redis::keys($cacheKey);
        if (!$token) {
            throw new JwtRefreshTokenExpiredException('刷新令牌会话已过期，请再次登录！');
        }
        return true;
    }

    /**
     * @desc: 生成设备缓存令牌
     * （1）登录时，判断该账号是否在其它设备登录，如果有，就请空之前key清除，
     * （2）重新设置key 。然后存储用户信息和ip地址拼接为key，存储在redis当中
     */
    public static function generateToken(array $args): void
    {
        $cacheKey = $args['cache_token_pre'] . $args['id'];
        $key = Redis::keys($cacheKey . ':*');
        if (!empty($key)) {
            Redis::del(current($key));
        }
        Redis::setex($cacheKey . ':' . $args['jti'], $args['access_exp'], $args['access_token']);
    }

    /**
     * @desc: 生成设备缓存刷新令牌
     * @param array $args
     */
    public static function generateRefreshToken(array $args): void
    {
        $cacheKey = $args['cache_refresh_token_pre'] . $args['id'];
        Redis::setex($cacheKey . ':' . $args['jti'], $args['refresh_exp'], $args['refresh_token']);
    }

    /**
     * @desc: 生成单设备缓存刷新令牌
     * @param array $args
     */
    public static function generateSingleRefreshToken(array $args): void
    {
        $cacheKey = $args['cache_refresh_token_pre'] . $args['id'];
        $keys = Redis::keys($cacheKey . ':*');
        if (!empty($keys)) {
            Redis::del(current($keys));
        }
        Redis::setex($cacheKey . ':' . $args['jti'], $args['refresh_exp'], $args['refresh_token']);
    }


    /**
     * @desc: 清理缓存令牌
     */
    public static function clearToken(string $pre, string $uid): bool
    {
        $token = Redis::keys($pre . $uid . ':*');
        if ($token) {
            Redis::del(current($token));
        }
        return true;
    }

    /**
     * @desc: 清理缓存刷新令牌
     */
    public static function clearRefreshToken(string $pre): bool
    {
        $token = Redis::keys($pre);
        if ($token) {
            Redis::del(current($token));
        }
        return true;
    }
}
