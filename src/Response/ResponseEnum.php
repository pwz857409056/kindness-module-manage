<?php

namespace Kindness\ModuleManage\Response;
/**
 * 200表示成功|400表示错误的请求|500表示内部服务器错误
 * Class ResponseEnum
 * @package App\Helpers
 */
class ResponseEnum
{
    const HTTP_OK = [200001, '操作成功'];
    const HTTP_ERROR = [500001, '操作失败'];
}
