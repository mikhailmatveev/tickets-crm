<?php

namespace Database\Seeders;

use App\Enums\User\Role as RoleEnum;
use App\Enums\User\Permission as PermissionEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Права
        Permission::create(['name' => PermissionEnum::CHANGE_TICKET_STATUS]);
        Permission::create(['name' => PermissionEnum::CHANGE_USER_PASSWORD]);
        Permission::create(['name' => PermissionEnum::CHANGE_USER_ROLE]);
        Permission::create(['name' => PermissionEnum::CREATE_USER]);
        Permission::create(['name' => PermissionEnum::DELETE_USER]);
        Permission::create(['name' => PermissionEnum::REPLY_ON_TICKET]);
        Permission::create(['name' => PermissionEnum::VIEW_API_DOCS]);
        Permission::create(['name' => PermissionEnum::VIEW_TELESCOPE]);
        Permission::create(['name' => PermissionEnum::VIEW_TICKET_DETAILS]);
        Permission::create(['name' => PermissionEnum::VIEW_TICKET_STATS]);
        Permission::create(['name' => PermissionEnum::VIEW_TICKETS]);

        // Роли
        $admin = Role::create(['name' => RoleEnum::ADMIN]);
        $manager = Role::create(['name' => RoleEnum::MANAGER]);

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
