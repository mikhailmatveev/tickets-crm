<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class UserUpdatePasswordData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $password
    ) {}
}
