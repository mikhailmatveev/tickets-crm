<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class UserCreateData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $role
    ) {}
}
