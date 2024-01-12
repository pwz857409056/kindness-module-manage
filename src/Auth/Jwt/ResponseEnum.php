<?php

namespace Kindness\ModuleManage\Auth\Jwt;
/**
 * @desc:响应码
 * @author: kindness<kindness8023@163.com>
 */
class ResponseEnum
{
    const USER_SERVICE_REFRESH_TOKEN_EXPIRED = [500106, 'Token expired'];//需要重新登录
    const USER_SERVICE_TOKEN_EXPIRED = [500107, 'Invalid token'];//需要刷新token
    const JWT_ERROR = [500108, 'JWT error'];
    const USER_SERVICE_OTHER_LOGIN_ERROR = [500109, '该账号已在其他设备登录，强制下线'];
}
