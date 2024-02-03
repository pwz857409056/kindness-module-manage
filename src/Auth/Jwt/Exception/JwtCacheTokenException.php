<?php


namespace Kindness\ModuleManage\Auth\Jwt\Exception;

use Throwable;

/**
 * 限制单设备登录时可能抛出的异常
 */
class JwtCacheTokenException extends JwtException
{
    /**
     * @param $message
     * @param $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "该账号已在其他设备登录，强制下线", $code = 500109, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
