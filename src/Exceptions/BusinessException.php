<?php

namespace Kindness\ModuleManage\Exceptions;

use Exception;


/**
 * @desc:业务异常
 * @author: kindness<kindness8023@163.com>
 */
class BusinessException extends Exception
{
    /**
     * 业务异常构造函数
     * @param array $codeResponse 状态码
     * @param string|array $info 自定义返回信息，不为空时会替换掉codeResponse 里面的message文字信息
     */
    public function __construct(array $codeResponse, string|array $info = '')
    {
        [$code, $message] = $codeResponse;
        $message = $info ? (is_array($info) ? implode('|', array_values($info)) : $info) : $message;
        parent::__construct($message, $code);
    }
}
