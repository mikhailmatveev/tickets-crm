<?php

namespace Tests\Feature;

use App\Enums\User\Role as RoleEnum;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserChangeRoleTest extends UserTest
{
    /**
     * Тест смены роли пользователю из-под админа (ожидаем 200-й ответ)
     * @return void
     */
    public function test_admin_can_change_role(): void
    {
        // Логинимся как админ
        $this->actingAsAdmin();
        // Тестовый пользователь
        $user = $this->createUser(RoleEnum::ADMIN);
        // PUT /api/user/{id}/role
        $response = $this->putJson("/api/user/{$user->id}/role", [
            'role_id' => $this->getValidRoleId(RoleEnum::MANAGER)
        ]);
        // 200-й ответ
        $response->assertOk();
        // Найдём тестового пользователя в базе
        $user = User::where('email', $user->email)
            ->firstOrFail()
        ;
        // Проверяем, что роль у пользователя осталась прежней
        $this->assertTrue($user->hasRole(RoleEnum::MANAGER));
    }

    /**
     * Тест смены роли из-под менеджера (ожидаем 403-й ответ)
     * @return void
     */
    public function test_manager_cannot_change_role(): void
    {
        // Логинимся как менеджер
        $this->actingAsManager();
        // Тестовый пользователь
        $user = $this->createUser(RoleEnum::ADMIN);
        // PUT /api/user/{id}/role
        $response = $this->putJson("/api/user/{$user->id}/role", [
            'role_id' => $this->getValidRoleId(RoleEnum::MANAGER)
        ]);
        // 403-й ответ
        $response->assertForbidden();
        // Найдём тестового пользователя в базе
        $user = User::where('email', $user->email)
            ->firstOrFail()
        ;
        // Проверяем, что роль у пользователя осталась прежней
        $this->assertTrue($user->hasRole(RoleEnum::ADMIN));
    }

    /**
     * Хелпер-метод получения ID роли по её имени
     * @param RoleEnum $role Роль
     * @return int ID роли в таблице roles
     */
    protected function getValidRoleId(RoleEnum $role): int
    {
        return Role::where('name', $role)
            ->first()
            ->id
        ;
    }
}
