<?php


namespace Kindness\ModuleManage\Auth\Jwt\Exception;

use RuntimeException;
use Throwable;

/**
 * token 验证异常
 */
class JwtTokenException extends RuntimeException
{
    /**
     * @param $message
     * @param $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "JWT error", $code = 500108, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
