<?php

namespace Kindness\ModuleManage\Auth\Jwt\Contracts;

interface TokenFactory
{
    public function generateToken(array $extend): array;

    public function verify(string $token): array;
}
