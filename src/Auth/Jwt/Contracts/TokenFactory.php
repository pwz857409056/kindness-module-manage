<?php

namespace Kindness\ModuleManage\Auth\Jwt\Contracts;

interface TokenFactory
{
    public function generateToken(array $extend): array;

    public function refreshToken(string $token): array;

    public function verify(int $tokenType, string $token): array;

    public function clear(): bool;
}