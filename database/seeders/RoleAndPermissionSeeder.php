<?php

namespace Database\Seeders;

use App\Enums\User\RoleEnum;
use App\Enums\User\PermissionEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Права
        Permission::firstOrCreate(['name' => PermissionEnum::CHANGE_TICKET_STATUS]);
        Permission::firstOrCreate(['name' => PermissionEnum::CHANGE_USER_PASSWORD]);
        Permission::firstOrCreate(['name' => PermissionEnum::CHANGE_USER_ROLE]);
        Permission::firstOrCreate(['name' => PermissionEnum::CREATE_USER]);
        Permission::firstOrCreate(['name' => PermissionEnum::DELETE_USER]);
        Permission::firstOrCreate(['name' => PermissionEnum::REPLY_ON_TICKET]);
        Permission::firstOrCreate(['name' => PermissionEnum::VIEW_API_DOCS]);
        Permission::firstOrCreate(['name' => PermissionEnum::VIEW_TELESCOPE]);
        Permission::firstOrCreate(['name' => PermissionEnum::VIEW_TICKET_DETAILS]);
        Permission::firstOrCreate(['name' => PermissionEnum::VIEW_TICKET_STATS]);
        Permission::firstOrCreate(['name' => PermissionEnum::VIEW_TICKETS]);

        // Роли
        $admin = Role::firstOrCreate(['name' => RoleEnum::ADMIN]);
        $manager = Role::firstOrCreate(['name' => RoleEnum::MANAGER]);

        // Админу назначаем полный доступ
        $admin->givePermissionTo(Permission::all());

        // Менеджеру назначаем только просмотр тикетов и статистики
        $manager->givePermissionTo([
            PermissionEnum::REPLY_ON_TICKET,
            PermissionEnum::VIEW_TICKET_DETAILS,
            PermissionEnum::VIEW_TICKET_STATS,
            PermissionEnum::VIEW_TICKETS
        ]);
    }
}
