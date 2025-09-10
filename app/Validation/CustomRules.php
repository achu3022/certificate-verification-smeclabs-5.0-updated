<?php

namespace App\Validation;

class CustomRules
{
    public function verify_password(string $str, string $hashedPassword, array $data = []): bool
    {
        return password_verify($str, $hashedPassword);
    }
}