<?php

namespace Kindness\ModuleManage\Contracts;
/**
 * @desc:业务服务接口
 * @author: kindness<kindness8023@163.com>
 */
interface ServiceInterface
{
    public function getResult(): mixed;

    public function getCode(): array;
}
