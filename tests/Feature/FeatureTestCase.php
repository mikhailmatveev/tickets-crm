<?php

namespace Tests\Feature;

use App\Enums\User\Role;
use App\Models\User;
use Tests\TestCase;

class FeatureTestCase extends TestCase
{
    /**
     * Хелпер-метод для авторизации под требуемой ролью
     * @param Role $role Роль
     * @return void
     */
    protected function actingAsRole(Role $role): void
    {
        $this->actingAs($this->createUser($role), 'sanctum');
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
