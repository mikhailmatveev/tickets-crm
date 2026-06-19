<?php

namespace App\Enums\User;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

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

    public function permissions(): array
    {
        return match($this) {
            self::ADMIN => Permission::all()->pluck('name')->toArray(),
            self::MANAGER => [
                PermissionEnum::CHANGE_TICKET_STATUS->value,
                PermissionEnum::REPLY_ON_TICKET->value,
                PermissionEnum::VIEW_TICKET_DETAILS->value,
                PermissionEnum::VIEW_TICKET_STATS->value,
                PermissionEnum::VIEW_TICKETS->value,
            ]
        };
    }
}
