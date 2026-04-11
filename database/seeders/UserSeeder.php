<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Кол-во фейковых администраторов
     */
    public const int ADMIN_USERS_COUNT = 1;

    /**
     * Кол-во фейковых менеджеров
     */
    public const int MANAGER_USERS_COUNT = 2;

    public function run(): void
    {
        // Создаёт фейковых администраторов в количестве, указанном в константе ADMIN_USERS_COUNT
        User::factory()
            ->count(self::ADMIN_USERS_COUNT)
            ->admin()
            ->create();
        // Создаёт фейковых менеджеров в количестве, указанном в константе MANAGER_USERS_COUNT
        User::factory()
            ->count(self::MANAGER_USERS_COUNT)
            ->manager()
            ->create();
    }
}
