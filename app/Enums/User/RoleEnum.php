<?php

namespace App\Enums\User;

use Illuminate\Support\Collection;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';

    public static function collection(): Collection
    {
        return collect(self::cases())->map(fn (RoleEnum $role) => $role->value);
    }

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
