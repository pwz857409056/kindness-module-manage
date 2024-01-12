<?php


namespace Kindness\ModuleManage\Auth\Jwt\Exception;

use Kindness\ModuleManage\Auth\Jwt\ResponseEnum;
use Kindness\ModuleManage\Exceptions\BusinessException;
use RuntimeException;
use Throwable;

/**
 * Class JwtCacheTokenException
 * @package App\Library\Jwt\Exception
 */
class JwtCacheTokenException extends RuntimeException
{
    /**
     * @throws BusinessException
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        throw new BusinessException(ResponseEnum::USER_SERVICE_OTHER_LOGIN_ERROR);
    }
}
