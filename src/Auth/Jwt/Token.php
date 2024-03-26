<?php

namespace Kindness\ModuleManage\Auth\Jwt;

use Exception;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Contracts\Foundation\Application;
use Kindness\ModuleManage\Auth\Jwt\Contracts\TokenFactory;
use Kindness\ModuleManage\Auth\Jwt\Exception\JwtCacheTokenException;
use Kindness\ModuleManage\Auth\Jwt\Exception\JwtConfigException;
use Kindness\ModuleManage\Auth\Jwt\Exception\JwtRefreshTokenExpiredException;
use Kindness\ModuleManage\Auth\Jwt\Exception\JwtTokenException;
use Kindness\ModuleManage\Auth\Jwt\Exception\JwtTokenExpiredException;
use UnexpectedValueException;

class Token implements TokenFactory
{
    protected Application $app;
    protected array $config = [];
    protected bool $encrypted = true;
    /**
     * @var bool 是否需要缓存token
     */
    private bool $isNeedCache = true;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function withConfig($option): static
    {
        $this->config = $option;
        return $this;
    }

    public function withEncrypted(bool $encrypted): static
    {
        $this->encrypted = $encrypted;
        return $this;
    }

    public function withIsNeedCache(bool $isNeedCache = true): static
    {
        $this->isNeedCache = $isNeedCache;
        return $this;
    }

    public function generateToken(array $extend): array
    {
        $config = $this->getConfig();
        if (!$config) {
            throw new JwtConfigException('jwt配置文件不存在');
        }
        if (!isset($extend['id'])) {
            throw new JwtTokenException('缺少全局唯一字段：id');
        }
        $token = [
            'token_type' => 'Bearer',
            'expires_in' => $config['access_exp'],
            'access_token' => $this->generateAccessToken($extend, $config),
            'refresh_token' => $this->generateRefreshToken($extend, $config),
        ];
        if ($this->isNeedCache) {
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
        }
        return $token;
    }

    private function generateAccessToken(array $extend, array $config): string
    {
        $payload = [
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
        $secretKey = $config['access_secret_key'];
        return $this->makeToken($payload, $secretKey, $config);
    }

    private function generateRefreshToken(array $extend, array $config): string
    {
        $payload = [
            'iss' => $config['iss'],
            'iat' => time(),
            'exp' => time() + $config['refresh_exp'],
            'aud' => $config['aud'],
            'leeway' => $config['leeway'],
            'jti' => $config['jti'],
            'sub' => $config['sub'],
            'nbf' => $config['nbf'],
            'extend' => $extend
        ];
        $secretKey = $config['refresh_secret_key'];
        return $this->makeToken($payload, $secretKey, $config);
    }

    /**
     * @desc: 生成令牌.
     */
    private function makeToken(array $payload, string $secretKey, array $config): string
    {
        $token = JWT::encode($payload, $secretKey, $config['algorithms']);
        if (!$this->encrypted) {
            return $token;
        }
        return (new Crypt())->withIv($config['iv'])->withMode($config['mode'])->encrypt($token, $config['passphrase']);
    }


    /**
     * @desc: 获取加密载体.
     */
    private function generatePayload(array $config, array $extend): array
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
     * @desc: 刷新令牌
     */
    public function refreshToken($token): array
    {
        $config = $this->getConfig();
        try {
            $extend = $this->verifyRefreshToken($token);
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
        $token = [
            'token_type' => 'Bearer',
            'expires_in' => $config['access_exp'],
            'access_token' => $this->generateAccessToken($extend['extend'], $config),
            'refresh_token' => $this->generateRefreshToken($extend['extend'], $config),
        ];
        if ($this->isNeedCache) {
            if ($config['is_single_device']) {
                RedisHandler::generateToken([
                    'id' => $extend['extend']['id'],
                    'jti' => $extend['jti'],
                    'access_token' => $token['access_token'],
                    'access_exp' => $config['access_exp'],
                    'cache_token_pre' => $config['cache_token_pre']
                ]);
                RedisHandler::generateSingleRefreshToken(
                    [
                        'id' => $extend['extend']['id'],
                        'jti' => $extend['jti'],
                        'refresh_token' => $token['refresh_token'],
                        'refresh_exp' => $config['refresh_exp'],
                        'cache_refresh_token_pre' => $config['cache_refresh_token_pre']
                    ]
                );
            } else {
                RedisHandler::generateRefreshToken([
                    'id' => $extend['extend']['id'],
                    'jti' => $extend['jti'],
                    'refresh_token' => $token['refresh_token'],
                    'refresh_exp' => $config['refresh_exp'],
                    'cache_refresh_token_pre' => $config['cache_refresh_token_pre']
                ]);
            }
        }
        return $token;
    }

    /**
     * @desc: 校验令牌
     */
    public function verifyAccessToken(string $token): array
    {
        $config = $this->getConfig();
        if ($this->encrypted) {
            $token = (new Crypt())->withIv($config['iv'])->withMode($config['mode'])->decrypt($token, $config['passphrase']);
        }
        $publicKey = $config['access_secret_key'];
        JWT::$leeway = $config['leeway'];
        $decoded = JWT::decode($token, new Key($publicKey, $config['algorithms']));
        $decodeToken = json_decode(json_encode($decoded), true);
        if ($config['strict'] && $decodeToken['aud'] != $config['aud']) {
            throw new JwtTokenException('无效token');
        }
        if ($this->isNeedCache) {
            if ($config['is_single_device']) {
                RedisHandler::verifyToken($config['cache_token_pre'], $decodeToken, $token);
            } else {
                RedisHandler::verifyRefreshToken($config['cache_refresh_token_pre'], $decodeToken);
            }
        }
        return $decodeToken;
    }

    /**
     * @desc: 校验令牌
     */
    public function verifyRefreshToken(string $token): array
    {
        $config = $this->getConfig();
        if ($this->encrypted) {
            $token = (new Crypt())->withIv($config['iv'])->withMode($config['mode'])->decrypt($token, $config['passphrase']);
        }
        $publicKey = $config['refresh_secret_key'];
        JWT::$leeway = $config['leeway'];
        $decoded = JWT::decode($token, new Key($publicKey, $config['algorithms']));
        $decodeToken = json_decode(json_encode($decoded), true);
        if ($config['strict'] && $decodeToken['aud'] != $config['aud']) {
            throw new JwtTokenException('无效token');
        }
        if ($this->isNeedCache) {
            if (!$config['is_single_device']) {
                RedisHandler::verifyRefreshToken($config['cache_refresh_token_pre'], $decodeToken);
            }
        }
        return $decodeToken;
    }

    private function getConfig(): array
    {
        if ($this->config) {
            return $this->config;
        }
        if ($this->app['config']->get('jwt')) {
            return $this->app['config']->get('jwt');
        }
        if (config('jwt')) {
            return config('jwt');
        }
        throw new JwtConfigException('jwt配置文件不存在');
    }

    /**
     * @desc: 验证令牌
     */
    public function verify(string $token = null): array
    {
        $token = $token ?? $this->getTokenFromHeaders();
        try {
            return $this->verifyAccessToken($token);
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
     * @desc: 获取Header头部authorization令牌
     */
    private function getTokenFromHeaders(): string
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
     * @desc: 获取当前登录ID
     */
    public function getCurrentId(): mixed
    {
        return $this->getExtendVal('id') ?? null;
    }

    /**
     * @desc: 获取当前用户信息
     */
    public function getUser(): mixed
    {
        $config = $this->getConfig();
        if (is_callable($config['user_model'])) {
            return $config['user_model']($this->getCurrentId()) ?? [];
        }
        return [];
    }

    /**
     * @desc: 获取指定令牌扩展内容字段的值
     */
    public function getExtendVal(string $key)
    {
        return $this->getTokenExtend()[$key] ?? '';
    }

    /**
     * @desc 获取指定令牌扩展内容
     */
    public function getExtend(): array
    {
        return $this->getTokenExtend();
    }

    /**
     * @desc: 获取扩展字段.
     */
    private function getTokenExtend(): array
    {
        return (array)$this->verify()['extend'];
    }

    /**
     * @desc: 获令牌有效期剩余时长.
     */
    public function getAccessTokenExp($token = null): int
    {
        return (int)$this->verifyAccessToken($token)['exp'] - time();
    }

    /**
     * @desc: 获令牌有效期剩余时长.
     */
    public function getRefreshTokenExp($token = null): int
    {
        return (int)$this->verifyRefreshToken($token)['exp'] - time();
    }

    /**
     * @desc: 注销令牌
     */
    public function clear($token = null): bool
    {
        $config = $this->getConfig();
        $token = $this->verify($token);
        RedisHandler::clearRefreshToken($config['cache_refresh_token_pre'] . $token['extend']['id'] . ':' . $token['jti']);
        if ($config['is_single_device']) {
            RedisHandler::clearToken($config['cache_token_pre'], $this->getCurrentId());
        }
        return true;
    }
}
