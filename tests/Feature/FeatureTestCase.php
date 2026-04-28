<?php

namespace Tests\Feature;

use App\Enums\User\Role;
use App\Models\User;
use Tests\TestCase;

class FeatureTestCase extends TestCase
{
    /**
     * Хелпер-метод для авторизации под админом
     * @return void
     */
    protected function actingAsAdmin(): void
    {
        $admin = $this->createUser(Role::ADMIN);
        $this->actingAs($admin, 'sanctum');
    }

    /**
     * Хелпер-метод для авторизации под менеджером
     * @return void
     */
    protected function actingAsManager(): void
    {
        $manager = $this->createUser(Role::MANAGER);
        $this->actingAs($manager, 'sanctum');
    }

    /**
     * Хелпер-метод создания пользователя с определённой ролью
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
