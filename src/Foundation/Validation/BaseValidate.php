<?php

namespace Kindness\ModuleManage\Foundation\Validation;

use Kindness\ModuleManage\Exceptions\BusinessException;
use Kindness\ModuleManage\Response\ResponseEnum;
use think\Validate;

class BaseValidate extends Validate
{
    /**
     * @throws BusinessException
     */
    public function validate($scene = ''): bool
    {
        $params = request()->all();
        $check = empty($scene) ? $this->check($params) : $this->scene($scene)->check($params);
        //开始判断验证
        if (!$check) {
            throw new BusinessException(ResponseEnum::HTTP_VALIDATE_ERROR, $this->getError());
        }
        return true;
    }
}