<?php


namespace Kindness\ModuleManage\Auth\Jwt\Exception;

use Throwable;

/**
 * 鉴权token过期，需要刷新token
 */
class JwtTokenExpiredException extends JwtException
{
    /**
     * @param $message
     * @param $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "Invalid token", $code = 500107, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
