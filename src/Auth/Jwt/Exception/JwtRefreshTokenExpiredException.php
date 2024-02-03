<?php


namespace Kindness\ModuleManage\Auth\Jwt\Exception;

use RuntimeException;
use Throwable;

/**
 * token过期，需要重新登录
 */
class JwtRefreshTokenExpiredException extends RuntimeException
{
    /**
     * @param $message
     * @param $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "Token expired", $code = 500106, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
