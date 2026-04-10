<?php

namespace App\Enums\User;

enum Role: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';

    public function middleware(): string
    {
        return 'role:' . $this->value;
    }

//    public static function values(self ...$roles): array
//    {
//        return array_map(
//            static fn (self $role) => $role->value,
//            $roles
//        );
//    }
}
