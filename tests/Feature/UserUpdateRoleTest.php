<?php

namespace Tests\Feature;

use App\Enums\User\Role as RoleEnum;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserUpdateRoleTest extends UserTest
{
    /**
     * Тест смены роли пользователя из-под админа (ожидаем 200-й ответ)
     * @return void
     */
    public function test_admin_can_update_role(): void
    {
        // Логинимся как админ
        $this->actingAsAdmin();

        // Создаём тестового пользователя, которому попытаемся изменить роль
        $targetUser = User::factory()
            ->admin()
            ->create()
        ;

        $payload = [
            'role_id' => Role::where('name', RoleEnum::MANAGER)
                ->first()
                ->id
        ];

        // PUT /api/user/{id}/role
        $response = $this->putJson("/api/user/{$targetUser->id}/role", $payload);

        // 200-й ответ
        $response->assertOk();

        // Загрузить повторно пользователя из базы в модель
        $targetUser->refresh();

        // Убедимся, что после выполнения запроса, у пользователя сменилась роль с админа на менеджера
        $this->assertTrue($targetUser->hasRole(RoleEnum::MANAGER));

        // Дополнительная проверка, что у пользователя уже нет роли админа
        $this->assertFalse($targetUser->hasRole(RoleEnum::ADMIN));
    }

    /**
     * Тест смены роли пользователя из-под менеджера (ожидаем 403-й ответ)
     * @return void
     */
    public function test_manager_cannot_update_role(): void
    {
        // Логинимся как менеджер
        $this->actingAsManager();

        // Создаём тестового пользователя, которому попытаемся изменить роль
        $targetUser = User::factory()
            ->admin()
            ->create()
        ;

        $payload = [
            'role_id' => Role::where('name', RoleEnum::MANAGER)
                ->first()
                ->id
        ];

        // PUT /api/user/{id}/role
        $response = $this->putJson("/api/user/{$targetUser->id}/role", $payload);

        // 403-й ответ
        $response->assertForbidden();

        // Загрузить повторно пользователя из базы в модель
        $targetUser->refresh();

        // Убедимся, что после выполнения запроса, у пользователя не поменялась роль с админа на менеджера
        $this->assertTrue($targetUser->hasRole(RoleEnum::ADMIN));
    }
}
