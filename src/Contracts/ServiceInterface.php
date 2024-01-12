<?php

namespace Kindness\ModuleManage\Contracts;

interface ServiceInterface
{
    public function getResult(): array;

    public function getCode(): array;
}
