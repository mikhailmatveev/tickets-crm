<?php

namespace Database\Seeders;

use App\Enums\User\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Кол-во фейковых администраторов
     */
    public const int ADMIN_USERS_COUNT = 2;

    /**
     * Кол-во фейковых менеджеров
     */
    public const int MANAGER_USERS_COUNT = 3;

    public function run(): void
    {
        $this->createAdminUsers();
        $this->createManagerUsers();
    }

    /**
     * Создаёт фейковых администраторов в количестве, указанном в константе ADMIN_USERS_COUNT
     * @return void
     */
    protected function createAdminUsers(): void
    {
        $adminUsers = User::factory()
            ->count(self::ADMIN_USERS_COUNT)
            ->create()
        ;
        foreach ($adminUsers as $user) {
            $user->assignRole(Role::ADMIN);
        }
    }

    /**
     * Создаёт фейковых менеджеров в количестве, указанном в константе MANAGER_USERS_COUNT
     * @return void
     */
    protected function createManagerUsers(): void
    {
        $managerUsers = User::factory()
            ->count(self::MANAGER_USERS_COUNT)
            ->create()
        ;
        foreach ($managerUsers as $user) {
            $user->assignRole(Role::MANAGER);
        }
    }
}
