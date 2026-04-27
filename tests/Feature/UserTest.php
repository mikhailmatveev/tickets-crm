<?php

namespace Tests\Feature;

use App\Enums\User\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Tests\TestCase;

class UserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * Хелпер-метод для авторизации под админом
     * @return void
     */
    protected function actingAsAdmin(): void
    {
        $admin = $this->createUser(Role::ADMIN);
        $this->be($admin, 'sanctum');
    }

    /**
     * Хелпер-метод для авторизации под менеджером
     * @return void
     */
    protected function actingAsManager(): void
    {
        $manager = $this->createUser(Role::MANAGER);
        $this->be($manager, 'sanctum');
    }

    /**
     * Хелпер-метод создания тестового пользователя
     * @param Role $role Роль
     * @return User Пользователь из модели User
     */
    protected function createUser(Role $role): User
    {
        return match ($role) {
            Role::ADMIN => User::factory()
                ->admin()
                ->create(),
            Role::MANAGER => User::factory()
                ->manager()
                ->create()
        };
    }
}
