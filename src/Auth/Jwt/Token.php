<?php


namespace Kindness\ModuleManage\Auth\Jwt;

use Exception;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Kindness\ModuleManage\Auth\Jwt\Exception\JwtCacheTokenException;
use Kindness\ModuleManage\Auth\Jwt\Exception\JwtConfigException;
use Kindness\ModuleManage\Auth\Jwt\Exception\JwtRefreshTokenExpiredException;
use Kindness\ModuleManage\Auth\Jwt\Exception\JwtTokenException;
use Kindness\ModuleManage\Auth\Jwt\Exception\JwtTokenExpiredException;
use UnexpectedValueException;

/**
 * Class Token
 * @package App\Library\Jwt
 */
class Token
{
    /**
     * access_token.
     */
    private const ACCESS_TOKEN = 1;

    /**
     * refresh_token.
     */
    private const REFRESH_TOKEN = 2;

    /**
     * @desc: 获取当前登录ID
     */
    public static function getCurrentId(): mixed
    {
        return self::getExtendVal('id') ?? 0;
    }

    /**
     * @desc: 获取当前用户信息
     */
    public static function getUser(): mixed
    {
        $config = self::getConfig();
        if (is_callable($config['user_model'])) {
            return $config['user_model'](self::getCurrentId()) ?? [];
        }
        return [];
    }

    /**
     * @desc: 获取指定令牌扩展内容字段的值
     */
    public static function getExtendVal(string $key)
    {
        return self::getTokenExtend()[$key] ?? '';
    }

    /**
     * @desc 获取指定令牌扩展内容
     */
    public static function getExtend(): array
    {
        return self::getTokenExtend();
    }

    /**
     * @desc: 刷新令牌
     */
    public static function refreshToken($token): array
    {
        $config = self::getConfig();
        try {
            $extend = self::verifyToken($token, self::REFRESH_TOKEN);
        } catch (SignatureInvalidException) {
            throw new JwtTokenException('刷新令牌无效');
        } catch (BeforeValidException) {
            throw new JwtTokenException('刷新令牌尚未生效');
        } catch (ExpiredException) {
            throw new JwtRefreshTokenExpiredException('刷新令牌会话已过期，请重新登录！');
        } catch (UnexpectedValueException) {
            throw new JwtTokenException('刷新令牌获取的扩展字段不存在');
        } catch (JwtCacheTokenException|Exception $exception) {
            throw new JwtRefreshTokenExpiredException($exception->getMessage());
        }
        $config['jti'] = $extend['jti'];
        $payload = self::generatePayload($config, $extend['extend']);
        $secretKey = self::getPrivateKey($config);
        $newToken['access_token'] = self::makeToken($payload['accessPayload'], $secretKey, $config);
        $refreshSecretKey = self::getPrivateKey($config, self::REFRESH_TOKEN);
        $newToken['refresh_token'] = self::makeToken($payload['refreshPayload'], $refreshSecretKey, $config);
        if ($config['is_single_device']) {
            RedisHandler::generateToken([
                'id' => $extend['extend']['id'],
                'jti' => $extend['jti'],
                'access_token' => $newToken['access_token'],
                'access_exp' => $config['access_exp'],
                'cache_token_pre' => $config['cache_token_pre']
            ]);
            RedisHandler::generateSingleRefreshToken(
                [
                    'id' => $extend['extend']['id'],
                    'jti' => $extend['jti'],
                    'refresh_token' => $newToken['refresh_token'],
                    'refresh_exp' => $config['refresh_exp'],
                    'cache_refresh_token_pre' => $config['cache_refresh_token_pre']
                ]
            );
        } else {
            RedisHandler::generateRefreshToken([
                'id' => $extend['extend']['id'],
                'jti' => $extend['jti'],
                'refresh_token' => $newToken['refresh_token'],
                'refresh_exp' => $config['refresh_exp'],
                'cache_refresh_token_pre' => $config['cache_refresh_token_pre']
            ]);
        }
        return $newToken;
    }

    /**
     * @desc: 生成令牌.
     */
    public static function generateToken(array $extend): array
    {
        if (!isset($extend['id'])) {
            throw new JwtTokenException('缺少全局唯一字段：id');
        }
        $config = self::getConfig();
        $payload = self::generatePayload($config, $extend);
        $secretKey = self::getPrivateKey($config);
        $token = [
            'token_type' => 'Bearer',
            'expires_in' => $config['access_exp'],
            'access_token' => self::makeToken($payload['accessPayload'], $secretKey, $config)
        ];
        $refreshSecretKey = self::getPrivateKey($config, self::REFRESH_TOKEN);
        $token['refresh_token'] = self::makeToken($payload['refreshPayload'], $refreshSecretKey, $config);
        if ($config['is_single_device']) {
            RedisHandler::generateToken([
                'id' => $extend['id'],
                'jti' => $config['jti'],
                'access_token' => $token['access_token'],
                'access_exp' => $config['access_exp'],
                'cache_token_pre' => $config['cache_token_pre']
            ]);
            RedisHandler::generateSingleRefreshToken(
                [
                    'id' => $extend['id'],
                    'jti' => $config['jti'],
                    'refresh_token' => $token['refresh_token'],
                    'refresh_exp' => $config['refresh_exp'],
                    'cache_refresh_token_pre' => $config['cache_refresh_token_pre']
                ]
            );
        } else {
            RedisHandler::generateRefreshToken([
                'id' => $extend['id'],
                'jti' => $config['jti'],
                'refresh_token' => $token['refresh_token'],
                'refresh_exp' => $config['refresh_exp'],
                'cache_refresh_token_pre' => $config['cache_refresh_token_pre']
            ]);
        }
        return $token;
    }

    /**
     * @desc: 验证令牌
     */
    public static function verify(int $tokenType = self::ACCESS_TOKEN, string $token = null): array
    {
        $token = $token ?? self::getTokenFromHeaders();
        try {
            return self::verifyToken($token, $tokenType);
        } catch (SignatureInvalidException) {
            throw new JwtTokenException('身份验证令牌无效');
        } catch (BeforeValidException) {
            throw new JwtTokenException('身份验证令牌尚未生效');
        } catch (ExpiredException) {
            throw new JwtTokenExpiredException('身份验证会话已过期！');
        } catch (UnexpectedValueException) {
            throw new JwtTokenException('获取的扩展字段不存在');
        } catch (JwtRefreshTokenExpiredException) {
            throw new JwtRefreshTokenExpiredException('身份验证会话已过期，请重新登录！!');
        } catch (JwtCacheTokenException $exception) {
            throw new JwtTokenExpiredException($exception->getMessage());
        }
    }

    /**
     * @desc: 获取扩展字段.
     */
    private static function getTokenExtend(): array
    {
        return (array)self::verify()['extend'];
    }

    /**
     * @desc: 获令牌有效期剩余时长.
     */
    public static function getTokenExp(int $tokenType = self::ACCESS_TOKEN): int
    {
        return (int)self::verify($tokenType)['exp'] - time();

    }

    /**
     * @desc: 获取Header头部authorization令牌
     */
    private static function getTokenFromHeaders(): string
    {
        $authorization = request()->header('Authorization');
        if (!$authorization || 'undefined' == $authorization) {
            throw new JwtTokenException('请求未携带authorization信息');
        }
        if (2 != count(explode(' ', $authorization))) {
            throw new JwtTokenException('Bearer验证中的凭证格式有误，中间必须有个空格');
        }
        [$type, $token] = explode(' ', $authorization);
        if ('Bearer' !== $type) {
            throw new JwtTokenException('接口认证方式需为Bearer');
        }
        return $token;
    }

    /**
     * @desc: 校验令牌
     */
    private static function verifyToken(string $token, int $tokenType): array
    {
        $config = self::getConfig();
        $decryptToken = (new Crypt())->withIv($config['iv'])->withMode($config['mode'])->decrypt($token, $config['passphrase']);
        $publicKey = self::ACCESS_TOKEN == $tokenType ? self::getPublicKey($config['algorithms']) : self::getPublicKey($config['algorithms'], self::REFRESH_TOKEN);
        JWT::$leeway = $config['leeway'];
        $decoded = JWT::decode($decryptToken, new Key($publicKey, $config['algorithms']));
        $decodeToken = json_decode(json_encode($decoded), true);
        if ($config['strict'] && $decodeToken['aud'] != $config['aud']) {
            throw new JwtTokenException('无效token');
        }
        if ($config['is_single_device'] && self::REFRESH_TOKEN != $tokenType) {
            RedisHandler::verifyToken($config['cache_token_pre'], $decodeToken, $token);
        } else {
            RedisHandler::verifyRefreshToken($config['cache_refresh_token_pre'], $decodeToken);
        }
        return $decodeToken;
    }

    /**
     * @desc: 生成令牌.
     */
    private static function makeToken(array $payload, string $secretKey, array $config): string
    {
        $token = JWT::encode($payload, $secretKey, $config['algorithms']);
        return (new Crypt())->withIv($config['iv'])->withMode($config['mode'])->encrypt($token, $config['passphrase']);
    }

    /**
     * @desc: 获取加密载体.
     */
    private static function generatePayload(array $config, array $extend): array
    {
        $basePayload = [
            'iss' => $config['iss'],
            'iat' => time(),
            'exp' => time() + $config['access_exp'],
            'aud' => $config['aud'],
            'leeway' => $config['leeway'],
            'jti' => $config['jti'],
            'sub' => $config['sub'],
            'nbf' => $config['nbf'],
            'extend' => $extend
        ];
        $resPayLoad['accessPayload'] = $basePayload;
        $basePayload['exp'] = time() + $config['refresh_exp'];
        $resPayLoad['refreshPayload'] = $basePayload;

        return $resPayLoad;
    }

    /**
     * @desc: 根据签名算法获取【公钥】签名值
     */
    private static function getPublicKey(string $algorithm, int $tokenType = self::ACCESS_TOKEN): string
    {
        $config = self::getConfig();
        switch ($algorithm) {
            case 'HS256':
                $key = self::ACCESS_TOKEN == $tokenType ? $config['access_secret_key'] : $config['refresh_secret_key'];
                break;
            case 'RS512':
            case 'RS256':
                $key = self::ACCESS_TOKEN == $tokenType ? $config['access_public_key'] : $config['refresh_public_key'];
                break;
            default:
                $key = $config['access_secret_key'];
        }

        return $key;
    }

    /**
     * @desc: 根据签名算法获取【私钥】签名值
     */
    private static function getPrivateKey(array $config, int $tokenType = self::ACCESS_TOKEN): string
    {
        switch ($config['algorithms']) {
            case 'HS256':
                $key = self::ACCESS_TOKEN == $tokenType ? $config['access_secret_key'] : $config['refresh_secret_key'];
                break;
            case 'RS512':
            case 'RS256':
                $key = self::ACCESS_TOKEN == $tokenType ? $config['access_private_key'] : $config['refresh_private_key'];
                break;
            default:
                $key = $config['access_secret_key'];
        }

        return $key;
    }

    /**
     * @desc: 获取配置文件
     */
    private static function getConfig(): array
    {
        $key = 'jwt';
        if (request()->plugin) {
            $key = implode('.', ['plugin', request()->plugin, 'jwt']);
        }
        $config = config($key);
        if (empty($config)) {
            throw new JwtConfigException('jwt配置文件不存在');
        }
        return $config;
    }

    /**
     * @desc: 注销令牌
     */
    public static function clear(): bool
    {
        $config = self::getConfig();
        $token = self::verify();
        RedisHandler::clearRefreshToken($config['cache_refresh_token_pre'] . $token['extend']['id'] . ':' . $token['jti']);
        if ($config['is_single_device']) {
            RedisHandler::clearToken($config['cache_token_pre'], self::getCurrentId());
        }
        return true;
    }
}
