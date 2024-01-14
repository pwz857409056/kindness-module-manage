<?php

namespace Kindness\ModuleManage\Contracts;

interface ServiceInterface
{
    public function getResult(): mixed;

    public function getCode(): array;
}
