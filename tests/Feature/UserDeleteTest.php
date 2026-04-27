<?php

namespace Tests\Feature;

use App\Enums\User\Role;

class UserDeleteTest extends UserTest
{
    /**
     * Тест удаления пользователя из-под админа (ожидаем 200-й ответ)
     * @return void
     */
    public function test_admin_can_delete_user(): void
    {
        // Логинимся как админ
        $this->actingAsAdmin();

        // Создаём тестового пользователя, которого попытаемся удалить
        $targetUser = $this->createUser(Role::MANAGER);

        // DELETE /api/user/{id}
        $response = $this->deleteJson("/api/user/{$targetUser->id}");

        // 200-й ответ
        $response->assertOk();

        $this->assertSoftDeleted('users', [
            'id' => $targetUser->id
        ]);
    }

    /**
     * Тест удаления пользователя из-под менеджера (ожидаем 403-й ответ)
     * @return void
     */
    public function test_manager_cannot_delete_user(): void
    {
        // Логинимся как менеджер
        $this->actingAsManager();

        // Создаём тестового пользователя, которого попытаемся удалить
        $targetUser = $this->createUser(Role::ADMIN);

        // DELETE /api/user/{id}
        $response = $this->deleteJson("/api/user/{$targetUser->id}");

        // 403-й ответ
        $response->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'deleted_at' => null
        ]);
    }
}
