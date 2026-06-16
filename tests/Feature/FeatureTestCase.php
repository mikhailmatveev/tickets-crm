<?php

namespace Tests\Feature;

use App\Enums\User\RoleEnum;
use App\Models\User;
use Tests\TestCase;

class FeatureTestCase extends TestCase
{
    /**
     * Хелпер-метод для авторизации под требуемой ролью
     * @param RoleEnum $role Роль
     * @return void
     */
    protected function actingAsRole(RoleEnum $role): void
    {
        $this->actingAs($this->createUser($role), 'sanctum');
    }

    /**
     * Хелпер-метод создания пользователя с определённой ролью
     * @param RoleEnum $role Роль
     * @return User Пользователь из модели User
     */
    protected function createUser(RoleEnum $role): User
    {
        return match ($role) {
            RoleEnum::ADMIN => User::factory()
                ->admin()
                ->create(),
            RoleEnum::MANAGER => User::factory()
                ->manager()
                ->create()
        };
    }
}
